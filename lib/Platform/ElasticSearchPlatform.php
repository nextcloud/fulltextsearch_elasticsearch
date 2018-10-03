<?php
/**
 * FullTextSearch_ElasticSearch - Use Elasticsearch to index the content of your nextcloud
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2018
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
 */

namespace OCA\FullTextSearch_ElasticSearch\Platform;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Exception;
use OCA\FullTextSearch\IFullTextSearchPlatform;
use OCA\FullTextSearch\IFullTextSearchProvider;
use OCA\FullTextSearch\Model\DocumentAccess;
use OCA\FullTextSearch\Model\Index;
use OCA\FullTextSearch\Model\IndexDocument;
use OCA\FullTextSearch\Model\Runner;
use OCA\FullTextSearch\Model\SearchRequest;
use OCA\FullTextSearch\Model\SearchResult;
use OCA\FullTextSearch_ElasticSearch\AppInfo\Application;
use OCA\FullTextSearch_ElasticSearch\Exceptions\AccessIsEmptyException;
use OCA\FullTextSearch_ElasticSearch\Exceptions\ConfigurationException;
use OCA\FullTextSearch_ElasticSearch\Service\ConfigService;
use OCA\FullTextSearch_ElasticSearch\Service\IndexService;
use OCA\FullTextSearch_ElasticSearch\Service\MiscService;
use OCA\FullTextSearch_ElasticSearch\Service\SearchService;
use OCP\AppFramework\QueryException;


class ElasticSearchPlatform implements IFullTextSearchPlatform {

	/** @var ConfigService */
	private $configService;

	/** @var IndexService */
	private $indexService;

	/** @var SearchService */
	private $searchService;

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
		return 'Elasticsearch';
	}


	/**
	 * @return string
	 */
	public function getVersion() {
		return '';
	}


	/**
	 * @return array
	 * @throws ConfigurationException
	 */
	public function getConfiguration() {

		$result = [];
		$hosts = $this->configService->getElasticHost();

		foreach ($hosts as $host) {
			$parsedHost = parse_url($host);
			$safeHost = $parsedHost['scheme'] . '://';
			if (array_key_exists('user', $parsedHost)) {
				$safeHost .= $parsedHost['user'] . ':' . '********' . '@';
			}
			$safeHost .= $parsedHost['host'];
			$safeHost .= ':' . $parsedHost['port'];

			$result[] = $safeHost;
		}

		return [
			'elastic_host'  => $result,
			'elastic_index' => $this->configService->getElasticIndex()
		];
	}


	/**
	 * @param Runner $runner
	 */
	public function setRunner(Runner $runner) {
		$this->runner = $runner;
	}

	/**
	 * @param $action
	 * @param bool $force
	 *
	 * @throws Exception
	 */
	private function updateRunnerAction($action, $force = false) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->updateAction($action, $force);
	}


	/**
	 * @param Index $index
	 * @param string $message
	 * @param string $exception
	 * @param int $sev
	 */
	private function updateNewIndexError($index, $message, $exception, $sev) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->newIndexError($index, $message, $exception, $sev);
	}


	/**
	 * @param Index $index
	 * @param string $message
	 * @param string $status
	 * @param int $type
	 */
	private function updateNewIndexResult($index, $message, $status, $type) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->newIndexResult($index, $message, $status, $type);
	}


	/**
	 * Called when loading the platform.
	 *
	 * Loading some container and connect to ElasticSearch.
	 *
	 * @throws ConfigurationException
	 * @throws QueryException
	 * @throws Exception
	 */
	public function loadPlatform() {
		$app = new Application();

		$container = $app->getContainer();
		$this->configService = $container->query(ConfigService::class);
		$this->indexService = $container->query(IndexService::class);
		$this->searchService = $container->query(SearchService::class);
		$this->miscService = $container->query(MiscService::class);

		try {
			$this->connectToElastic($this->configService->getElasticHost());
		} catch (ConfigurationException $e) {
			throw $e;
		}
	}


	/**
	 * not used yet.
	 *
	 * @return bool
	 */
	public function testPlatform() {
		return $this->client->ping();
	}


	/**
	 * called before any index
	 *
	 * We create a general index.
	 *
	 * @throws ConfigurationException
	 * @throws BadRequest400Exception
	 */
	public function initializeIndex() {
		$this->indexService->initializeIndex($this->client);
	}


	/**
	 * resetIndex();
	 *
	 * Called when admin wants to remove an index specific to a $provider.
	 * $provider can be null, meaning a reset of the whole index.
	 *
	 * @param string $providerId
	 *
	 * @throws ConfigurationException
	 */
	public function resetIndex($providerId) {
		if ($providerId === 'all') {
			$this->indexService->resetIndexAll($this->client);
		} else {
			$this->indexService->resetIndex($this->client, $providerId);
		}
	}


	/**
	 * @deprecated
	 *
	 * @param IFullTextSearchProvider $provider
	 * @param $documents
	 */
	public function indexDocuments(IFullTextSearchProvider $provider, $documents) {

	}


	/**
	 * @param IFullTextSearchProvider $provider
	 * @param IndexDocument $document
	 *
	 * @return Index
	 */
	public function indexDocument(IFullTextSearchProvider $provider, IndexDocument $document) {

		$document->initHash();

		try {
			$result = $this->indexService->indexDocument($this->client, $provider, $document);

			$index = $this->indexService->parseIndexResult($document->getIndex(), $result);

			$this->updateNewIndexResult(
				$document->getIndex(), json_encode($result), 'ok',
				Runner::RESULT_TYPE_SUCCESS
			);

			return $index;
		} catch (Exception $e) {
			$this->updateNewIndexResult(
				$document->getIndex(), '', 'issue while indexing, testing with empty content',
				Runner::RESULT_TYPE_WARNING
			);

			$this->manageIndexErrorException($document, $e);
		}

		try {
			$result = $this->indexDocumentError($provider, $document, $e);
			$index = $this->indexService->parseIndexResult($document->getIndex(), $result);

			$this->updateNewIndexResult(
				$document->getIndex(), json_encode($result), 'ok',
				Runner::RESULT_TYPE_WARNING
			);

			return $index;
		} catch (Exception $e) {
			$this->updateNewIndexResult(
				$document->getIndex(), '', 'fail',
				Runner::RESULT_TYPE_FAIL
			);
			$this->manageIndexErrorException($document, $e);
		}

		return $document->getIndex();
	}


	/**
	 * @param IFullTextSearchProvider $provider
	 * @param IndexDocument $document
	 * @param Exception $e
	 *
	 * @return array
	 * @throws AccessIsEmptyException
	 * @throws ConfigurationException
	 * @throws \Exception
	 */
	private function indexDocumentError(
		IFullTextSearchProvider $provider, IndexDocument $document, Exception $e
	) {

		$this->updateRunnerAction('indexDocumentWithoutContent', true);

		$document->setContent('');
//		$index = $document->getIndex();
//		$index->unsetStatus(Index::INDEX_CONTENT);

		$result = $this->indexService->indexDocument($this->client, $provider, $document);

		return $result;
	}


	/**
	 * @param IndexDocument $document
	 * @param Exception $e
	 */
	private function manageIndexErrorException(IndexDocument $document, Exception $e) {

		$message = $this->parseIndexErrorException($e);
		$document->getIndex()
				 ->addError($message, get_class($e), Index::ERROR_SEV_3);
		$this->updateNewIndexError(
			$document->getIndex(), $message, get_class($e), Index::ERROR_SEV_3
		);
	}


	/**
	 * @param Exception $e
	 *
	 * @return string
	 */
	private function parseIndexErrorException(Exception $e) {

		$arr = json_decode($e->getMessage(), true);
		if (!is_array($arr)) {
			return $e->getMessage();
		}

		if (array_key_exists('reason', $arr['error']['root_cause'][0])) {
			return $arr['error']['root_cause'][0]['reason'];
		}

		return $e->getMessage();
	}


	/**
	 * {@inheritdoc}
	 * @throws ConfigurationException
	 */
	public function deleteIndexes($indexes) {
		try {
			$this->indexService->deleteIndexes($this->client, $indexes);
		} catch (ConfigurationException $e) {
			throw $e;
		}
	}


	/**
	 * {@inheritdoc}
	 * @throws ConfigurationException
	 * @throws Exception
	 */
	public function searchDocuments(
		IFullTextSearchProvider $provider, DocumentAccess $access, SearchRequest $request
	) {
		return null;
//		return $this->searchService->searchDocuments($this->client, $provider, $access, $request);
	}



	/**
	 * {@inheritdoc}
	 * @throws Exception
	 */
	public function searchRequest(SearchResult $result, DocumentAccess $access) {
		$this->searchService->searchRequest($this->client, $result, $access);
	}


	/**
	 * @param string $providerId
	 * @param string $documentId
	 *
	 * @return IndexDocument
	 * @throws ConfigurationException
	 */
	public function getDocument($providerId, $documentId) {
		return $this->searchService->getDocument($this->client, $providerId, $documentId);
	}


	/**
	 * @param array $hosts
	 *
	 * @throws Exception
	 */
	private function connectToElastic($hosts) {

		try {
			$hosts = array_map([MiscService::class, 'noEndSlash'], $hosts);
			$this->client = ClientBuilder::create()
										 ->setHosts($hosts)
										 ->setRetries(3)
										 ->build();

//		}
//		catch (CouldNotConnectToHost $e) {
//			$this 'CouldNotConnectToHost';
//			$previous = $e->getPrevious();
//			if ($previous instanceof MaxRetriesException) {
//				echo "Max retries!";
//			}
		} catch (Exception $e) {
			throw $e;
//			echo ' ElasticSearchPlatform::load() Exception --- ' . $e->getMessage() . "\n";
		}
	}


}