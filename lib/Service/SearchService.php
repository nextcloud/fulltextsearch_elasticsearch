<?php
declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Service;


use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Client;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\ClientResponseException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\MissingParameterException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\ServerResponseException;
use Exception;
use OC\FullTextSearch\Model\DocumentAccess;
use OC\FullTextSearch\Model\IndexDocument;
use OCA\FullTextSearch_Elasticsearch\Exceptions\ConfigurationException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\SearchQueryGenerationException;
use OCA\FullTextSearch_Elasticsearch\Tools\Traits\TArrayTools;
use OCP\FullTextSearch\Model\IDocumentAccess;
use OCP\FullTextSearch\Model\IIndexDocument;
use OCP\FullTextSearch\Model\ISearchResult;
use Psr\Log\LoggerInterface;


/**
 * Class SearchService
 *
 * @package OCA\FullTextSearch_Elasticsearch\Service
 */
class SearchService {
	use TArrayTools;

	public function __construct(
		private SearchMappingService $searchMappingService,
		private LoggerInterface $logger
	) {
	}

	/**
	 * @param Client $client
	 * @param ISearchResult $searchResult
	 * @param IDocumentAccess $access
	 *
	 * @throws Exception
	 */
	public function searchRequest(
		Client $client,
		ISearchResult $searchResult,
		IDocumentAccess $access
	): void {
		try {
			$this->logger->debug('New search request', ['searchResult' => $searchResult]);
			$query = $this->searchMappingService->generateSearchQuery(
				$searchResult->getRequest(), $access, $searchResult->getProvider()
																   ->getId()
			);
		} catch (SearchQueryGenerationException $e) {
			return;
		}

		try {
			$this->logger->debug('Searching ES', ['params' => $query['params'] ?? []]);
			$result = $client->search($query['params']);
		} catch (Exception $e) {
			$this->logger->debug(
				'exception while searching',
				[
					'exception' => $e,
					'searchResult.Request' => $searchResult->getRequest(),
					'query' => $query
				]
			);
			throw $e;
		}

		$this->logger->debug('result from ES', ['result' => $result]);
		$this->updateSearchResult($searchResult, $result->asArray());

		foreach ($result['hits']['hits'] as $entry) {
			$searchResult->addDocument($this->parseSearchEntry($entry, $access->getViewerId()));
		}

		$this->logger->debug('Search Result', ['searchResult' => $searchResult]);
	}


	/**
	 * @param Client $client
	 * @param string $providerId
	 * @param string $documentId
	 *
	 * @return IIndexDocument
	 * @throws ConfigurationException
	 * @throws ClientResponseException
	 * @throws MissingParameterException
	 * @throws ServerResponseException
	 */
	public function getDocument(
		Client $client,
		string $providerId,
		string $documentId
	): IIndexDocument {
		$query = $this->searchMappingService->getDocumentQuery($providerId, $documentId);
		$result = $client->get($query);

		$access = new DocumentAccess($result['_source']['owner']);
		$access->setUsers($result['_source']['users']);
		$access->setGroups($result['_source']['groups']);
		$access->setCircles($result['_source']['circles']);
		$access->setLinks($result['_source']['links']);

		$index = new IndexDocument($providerId, $documentId);
		$index->setAccess($access);
		$index->setMetaTags($result['_source']['metatags']);
		$index->setSubTags($result['_source']['subtags']);
		$index->setTags($result['_source']['tags']);
//		$index->setMore($result['_source']['more']);
		$index->setHash($result['_source']['hash']);
		$index->setModifiedTime($result['_source']['lastModified'] ?? 0);
		$index->setSource($result['_source']['source']);
		$index->setTitle($result['_source']['title']);
		$index->setParts($result['_source']['parts']);

		$this->getDocumentInfos($index, $result['_source']);

		$content = $this->get('content', $result['_source'], '');
		$index->setContent($content);

		return $index;
	}


	/**
	 * @param IndexDocument $index
	 * @param array $source
	 */
	private function getDocumentInfos(IndexDocument $index, array $source): void {
		$ak = array_keys($source);
		foreach ($ak as $k) {
			if (str_starts_with($k, 'info_')) {
				continue;
			}
			$value = $source[$k];
			if (is_array($value)) {
				$index->setInfoArray($k, $value);
				continue;
			}

			if (is_bool($value)) {
				$index->setInfoBool($k, $value);
				continue;
			}

			if (is_numeric($value)) {
				$index->setInfoInt($k, (int)$value);
				continue;
			}

			$index->setInfo($k, (string)$value);
		}
	}


	/**
	 * @param ISearchResult $searchResult
	 * @param array $result
	 */
	private function updateSearchResult(ISearchResult $searchResult, array $result): void {
		$searchResult->setRawResult(json_encode($result));

		$total = $result['hits']['total'];
		if (is_array($total)) {
			$total = $total['value'];
		}

		$searchResult->setTotal($total);
		$searchResult->setMaxScore($this->getInt('max_score', $result['hits'], 0));
		$searchResult->setTime($result['took']);
		$searchResult->setTimedOut($result['timed_out']);
	}


	/**
	 * @param array $entry
	 * @param string $viewerId
	 *
	 * @return IIndexDocument
	 */
	private function parseSearchEntry(array $entry, string $viewerId): IIndexDocument {
		$access = new DocumentAccess();
		$access->setViewerId($viewerId);

		list($providerId, $documentId) = explode(':', $entry['_id'], 2);
		$document = new IndexDocument($providerId, $documentId);
		$document->setAccess($access);
		$document->setHash($this->get('hash', $entry['_source']));
		$document->setModifiedTime($this->getInt('lastModified', $entry['_source']));
		$document->setScore($this->get('_score', $entry, '0'));
		$document->setSource($this->get('source', $entry['_source']));
		$document->setTitle($this->get('title', $entry['_source']));

		$document->setExcerpts(
			$this->parseSearchEntryExcerpts(
				(array_key_exists('highlight', $entry)) ? $entry['highlight'] : []
			)
		);

		return $document;
	}


	private function parseSearchEntryExcerpts(array $highlights): array {
		$result = [];
		foreach (array_keys($highlights) as $source) {
			foreach ($highlights[$source] as $highlight) {
				$result[] =
					[
						'source' => $source,
						'excerpt' => $highlight
					];
			}
		}

		return $result;
	}
}

