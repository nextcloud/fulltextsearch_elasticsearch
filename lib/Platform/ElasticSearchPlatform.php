<?php
/**
 * FullNextSearch - Full Text Search your Nextcloud.
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2017
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 */

namespace OCA\FullNextSearch_ElasticSearch\Platform;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\Curl\CouldNotConnectToHost;
use Elasticsearch\Common\Exceptions\MaxRetriesException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Exception;
use OCA\FullNextSearch\INextSearchPlatform;
use OCA\FullNextSearch\INextSearchProvider;
use OCA\FullNextSearch\Model\DocumentAccess;
use OCA\FullNextSearch\Model\Index;
use OCA\FullNextSearch\Model\IndexDocument;
use OCA\FullNextSearch\Model\Runner;
use OCA\FullNextSearch\Model\SearchResult;
use OCA\FullNextSearch_ElasticSearch\AppInfo\Application;
use OCA\FullNextSearch_ElasticSearch\Exceptions\ConfigurationException;
use OCA\FullNextSearch_ElasticSearch\Service\ConfigService;
use OCA\FullNextSearch_ElasticSearch\Service\MiscService;


class ElasticSearchPlatform implements INextSearchPlatform {

	/** @var ConfigService */
	private $configService;

	/** @var MiscService */
	private $miscService;

	/** @var Client */
	private $client;

	/** @var Runner */
	private $runner;

	/**
	 * return a unique Id of the platform.
	 */
	public function getId() {
		return 'elastic_search';
	}

	/**
	 * return a unique Id of the platform.
	 */
	public function getName() {
		return 'ElasticSearch';
	}


	public function getClient() {
		return $this->client;
	}


	/**
	 * @param Runner $runner
	 */
	public function setRunner(Runner $runner) {
		$this->runner = $runner;
	}

	/**
	 * @param $action
	 */
	private function updateRunner($action) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->update($action);
	}


	/**
	 * @param $line
	 */
	private function outputRunner($line) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->output($line);
	}


	/**
	 * Called when loading the platform.
	 *
	 * Loading some container and connect to ElasticSearch.
	 *
	 * @throws ConfigurationException
	 */
	public function loadPlatform() {
		$app = new Application();

		$container = $app->getContainer();
		$this->configService = $container->query(ConfigService::class);
		$this->miscService = $container->query(MiscService::class);

		try {
			$this->connectToElastic($this->configService->getElasticHost());
		} catch (ConfigurationException $e) {
			throw $e;
		}
	}


	/**
	 * not used yet.
	 */
	public function testPlatform() {
	}


	/**
	 * called before any index
	 *
	 * We create a general index.
	 *
	 * @param INextSearchProvider $provider
	 *
	 * @throws ConfigurationException
	 */
	public function initializeIndex(INextSearchProvider $provider) {

		try {
			if (!$this->client->indices()
							  ->exists($this->generateGlobalMap(false))) {

				$this->client->indices()
							 ->create($this->generateGlobalMap());
				$this->client->ingest()
							 ->putPipeline($this->generateGlobalIngest());

			}
		} catch (BadRequest400Exception $e) {
			throw new ConfigurationException(
				'Check your user/password and the index assigned to that cloud'
			);
		}

		$provider->onInitializingIndex($this);
	}


	/**
	 * removeIndex();
	 *
	 * Called when admin wants to remove an index specific to a $provider.
	 * $provider can be null, meaning a reset of the whole index.
	 *
	 * @param INextSearchProvider|null $provider
	 */
	public function removeIndex($provider) {

		if ($provider instanceof INextSearchProvider) {
			// TODO: need to specify the map to remove
			// TODO: need to remove entries with type=providerId
			$provider->onRemovingIndex($this);
		}

		try {
			$this->client->ingest()
						 ->deletePipeline($this->generateGlobalIngest(false));
		} catch (Missing404Exception $e) {
			/* 404Exception will means that the mapping for that provider does not exist */
		}

		try {
			$this->client->indices()
						 ->delete($this->generateGlobalMap(false));
		} catch (Missing404Exception $e) {
			/* 404Exception will means that the mapping for that provider does not exist */
		}

	}


	/**
	 * {@inheritdoc}
	 */
	public function indexDocuments(INextSearchProvider $provider, $documents) {
		$indexes = [];
		foreach ($documents as $document) {
			$index = $this->indexDocument($provider, $document);
			if ($index !== null) {
				$indexes[] = $index;
			}
		}

		return $indexes;
	}


	/**
	 * {@inheritdoc}
	 */
	public function indexDocument(INextSearchProvider $provider, IndexDocument $document) {

		$this->updateRunner('indexDocument');
		$index = $document->getIndex();

		if ($index->isStatus(Index::STATUS_REMOVE_DOCUMENT)) {
			$result = $this->indexDocumentRemove($provider, $document);
		} else if ($index->isStatus(Index::STATUS_INDEX_DONE)) {
			$result = $this->indexDocumentUpdate($provider, $document);
		} else {
			$result = $this->indexDocumentNew($provider, $document);
		}


		$this->outputRunner('Indexing: ' . $document->getTitle() . ' ' . json_encode($result) . "\n");

		return $this->parseIndexResult($document->getIndex(), $result);
	}


	/**
	 * @param INextSearchProvider $provider
	 * @param IndexDocument $document
	 *
	 * @return array
	 */
	private function indexDocumentNew(INextSearchProvider $provider, IndexDocument $document) {
		$index = [
			'index' =>
				[
					'index' => $this->configService->getElasticIndex(),
					'id'    => $document->getId(),
					'type'  => $provider->getId(),
					'body'  => $this->generateIndexBody($document)
				]
		];

		$this->onIndexingDocument($provider, $document, $index);

		return $this->client->index($index['index']);
	}


	/**
	 * @param INextSearchProvider $provider
	 * @param IndexDocument $document
	 *
	 * @return array
	 */
	private function indexDocumentUpdate(INextSearchProvider $provider, IndexDocument $document) {
		$index = [
			'index' =>
				[
					'index' => $this->configService->getElasticIndex(),
					'id'    => $document->getId(),
					'type'  => $provider->getId(),
					'body'  => ['doc' => $this->generateIndexBody($document)]
				]
		];

		$this->onIndexingDocument($provider, $document, $index);

		return $this->client->update($index['index']);
	}


	/**
	 * @param INextSearchProvider $provider
	 * @param IndexDocument $document
	 *
	 * @return array
	 */
	private function indexDocumentRemove(INextSearchProvider $provider, IndexDocument $document) {
		$index = [
			'index' =>
				[
					'index' => $this->configService->getElasticIndex(),
					'id'    => $document->getId(),
					'type'  => $provider->getId()
				]
		];

		return $this->client->delete($index['index']);
	}


	/**
	 * @param INextSearchProvider $provider
	 * @param IndexDocument $document
	 * @param array $arr
	 */
	private function onIndexingDocument(INextSearchProvider $provider, IndexDocument $document, &$arr) {
		if ($document->isContentEncoded() === IndexDocument::ENCODED_BASE64) {
			$arr['index']['pipeline'] = 'attachment';
		}

		$provider->onIndexingDocument($this, $arr);
	}


	/**
	 * @param IndexDocument $document
	 *
	 * @return array
	 */
	private function generateIndexBody(IndexDocument $document) {

		$body = [];
		$access = $document->getAccess();
		if ($access !== null) {
			$body = [
				'owner'   => $access->getOwnerId(),
				'users'   => $access->getUsers(),
				'groups'  => $access->getGroups(),
				'circles' => $access->getCircles()
			];
		}

		$body['tags'] = $document->getTags();

		if ($document->getTitle() !== null) {
			$body['title'] = $document->getTitle();
		}

		if ($document->getContent() !== null) {
			$body['content'] = $document->getContent();
		}

		return array_merge($document->getInfoAll(), $body);
	}


	/**
	 * @param Index $index
	 * @param array $result
	 *
	 * @return Index
	 */
	private function parseIndexResult(Index $index, array $result) {

		if ($index->isStatus(Index::STATUS_REMOVE_DOCUMENT)) {
			$index->setStatus(Index::STATUS_DOCUMENT_REMOVED);

			return $index;
		}

		// TODO: parse result
		$index->setLastIndex();
		$index->setStatus(Index::STATUS_INDEX_DONE, true);

		return $index;
	}


	/**
	 * {@inheritdoc}
	 */
	public function searchDocuments(INextSearchProvider $provider, DocumentAccess $access, $string) {

		$query = [
			'params'    => $this->generateSearchQuery($provider, $access, $string),
			'query'     => $string,
			'requester' => $access->getViewerId()
		];

		$provider->onSearchingQuery($this, $query);

		$result = $this->client->search($query['params']);
		$searchResult = $this->generateSearchResultFromResult($result);
		$searchResult->setProvider($provider);

		foreach ($result['hits']['hits'] as $entry) {
			$searchResult->addDocument(
				$this->parseSearchEntry($provider->getId(), $entry, $access->getViewerId())
			);
		}

		return $searchResult;
	}


	/**
	 * @param string $host
	 */
	private function connectToElastic($host) {

		try {
			$hosts = [MiscService::noEndSlash($host)];
			$this->client = ClientBuilder::create()
										 ->setHosts($hosts)
										 ->setRetries(2)
										 ->build();

		} catch (CouldNotConnectToHost $e) {
			echo 'CouldNotConnectToHost';
			$previous = $e->getPrevious();
			if ($previous instanceof MaxRetriesException) {
				echo "Max retries!";
			}
		} catch (Exception $e) {
			echo ' ElasticSearchPlatform::load() Exception --- ' . $e->getMessage() . "\n";
		}
	}


	/**
	 * @param INextSearchProvider $provider
	 * @param DocumentAccess $access
	 * @param string $str
	 *
	 * @return array
	 */
	private function generateSearchQuery(INextSearchProvider $provider, DocumentAccess $access, $str) {

		$params = [
			'index' => $this->configService->getElasticIndex(),
			'type'  => $provider->getId()
		];

		$bool = [];
		$bool['must']['bool']['should'] =
			$this->generateSearchQueryContent($str);
		$bool['filter']['bool']['should'] =
			$this->generateSearchQueryAccess($access);

		$params['body']['query']['bool'] = $bool;

		$params['body']['highlight'] = $this->generateSearchHighlighting();

		return $params;
	}


	/**
	 * @param string $string
	 *
	 * @return array
	 */
	private function generateSearchQueryContent($string) {
		$queryTitle = $queryContent = [];
		$words = explode(' ', $string);
		foreach ($words as $word) {

			$kw = 'prefix';
			$this->modifySearchQueryContentOnCompleteWord($kw, $word);

			array_push($queryTitle, [$kw => ['title' => $word]]);
			array_push($queryContent, [$kw => ['content' => $word]]);
		}

		return [
			['bool' => ['must' => $queryTitle]],
			['bool' => ['must' => $queryContent]]
		];
	}


	/**
	 * @param string $kw
	 * @param string $word
	 */
	private function modifySearchQueryContentOnCompleteWord(&$kw, &$word) {
		if (substr($word, 0, 1) !== '"' || substr($word, -1) !== '"') {
			return;
		}

		$kw = 'match';
		$word = substr($word, 1, -1);
	}


	/**
	 * @param DocumentAccess $access
	 *
	 * @return array
	 */
	private function generateSearchQueryAccess(DocumentAccess $access) {

		$query = [];
		$query[] = ['match' => ['owner' => $access->getViewerId()]];
		$query[] = ['match' => ['users' => $access->getViewerId()]];

		foreach ($access->getGroups() as $group) {
			$query[] = ['match' => ['groups' => $group]];
		}

		foreach ($access->getCircles() as $circle) {
			['match' => ['circles' => $circle]];
		}

		return $query;
	}


	private function generateSearchHighlighting() {
		return [
			'fields'    => ['content' => new \stdClass()],
			'pre_tags'  => [''],
			'post_tags' => ['']
		];
	}

	/**
	 * @param array $result
	 *
	 * @return SearchResult
	 */
	private function generateSearchResultFromResult($result) {
		$searchResult = new SearchResult();
		$searchResult->setRawResult(json_encode($result));

		return $searchResult;
	}


	/**
	 * @param string $providerId
	 * @param array $entry
	 * @param string $viewerId
	 *
	 * @return IndexDocument
	 */
	private function parseSearchEntry($providerId, $entry, $viewerId) {
		$access = new DocumentAccess();
		$access->setViewerId($viewerId);

		$document = new IndexDocument($providerId, $entry['_id']);
		$document->setAccess($access);
		$document->setExcerpts(
			(array_key_exists('highlight', $entry)) ? $entry['highlight']['content'] : []
		);
		$document->setScore($entry['_score']);
		$document->setTitle(
			(array_key_exists('title', $entry['_source'])) ? $entry['_source']['title'] : ''
		);

		return $document;
	}


	/**
	 * @param bool $complete
	 *
	 * @return array
	 */
	private function generateGlobalMap($complete = true) {

		$params = [
			'index' => $this->configService->getElasticIndex()
		];

		if ($complete === false) {
			return $params;
		}

		$params['body'] = [
			'settings' => [
				'analysis' => [
					'filter'      => [
						'shingle' => [
							'type' => 'shingle'
						]
					],
					'char_filter' => [
						'pre_negs'  => [
							'type'        => 'pattern_replace',
							'pattern'     => '(\\w+)\\s+((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\b',
							'replacement' => '~$1 $2'
						],
						'post_negs' => [
							'type'        => 'pattern_replace',
							'pattern'     => '\\b((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\s+(\\w+)',
							'replacement' => '$1 ~$2'
						]
					],
					'analyzer'    => [
						'analyzer' => [
							'type'      => 'custom',
							'tokenizer' => 'standard',
							'filter'    => ['lowercase', 'stop', 'kstem']
						]
					]
				]
			],
			'mappings' => [
				'_default_' => [
					'properties' => [
						'title'    => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'content'  => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'owner'    => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'users'    => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'groups'   => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'circles'  => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'combined' => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes'
						],
						'topics'   => [
							'type'  => 'text',
							'index' => 'not_analyzed'
						],
						'places'   => [
							'type'  => 'text',
							'index' => 'not_analyzed'
						]
					]
				]
			]
		];

		return $params;
	}


	/**
	 * @param bool $complete
	 *
	 * @return array
	 */
	private function generateGlobalIngest($complete = true) {

		$params = ['id' => 'attachment'];

		if ($complete === false) {
			return $params;
		}

		$params['body'] = [
			'description' => 'attachment',
			'processors'  => [
				[
					'attachment' => [
						'field'         => 'content',
						'indexed_chars' => -1
					],
					'set'        => [
						'field' => 'content',
						'value' => '{{ attachment.content }}'
					],
					'remove'     => ['field' => 'attachment.content']
				]
			]
		];

		return $params;
	}

}