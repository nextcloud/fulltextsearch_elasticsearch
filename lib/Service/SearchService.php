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

namespace OCA\FullTextSearch_ElasticSearch\Service;

use Elasticsearch\Client;
use Exception;
use OCA\FullTextSearch\IFullTextSearchProvider;
use OCA\FullTextSearch\Model\DocumentAccess;
use OCA\FullTextSearch\Model\IndexDocument;
use OCA\FullTextSearch\Model\SearchRequest;
use OCA\FullTextSearch\Model\SearchResult;
use OCA\FullTextSearch_ElasticSearch\Exceptions\ConfigurationException;
use OCA\FullTextSearch_ElasticSearch\Exceptions\SearchQueryGenerationException;

class SearchService {


	/** @var SearchMappingService */
	private $searchMappingService;

	/** @var MiscService */
	private $miscService;


	/**
	 * SearchService constructor.
	 *
	 * @param SearchMappingService $searchMappingService
	 * @param MiscService $miscService
	 */
	public function __construct(
		SearchMappingService $searchMappingService, MiscService $miscService
	) {
		$this->searchMappingService = $searchMappingService;
		$this->miscService = $miscService;
	}


//	/**
//	 * @param Client $client
//	 * @param IFullTextSearchProvider $provider
//	 * @param DocumentAccess $access
//	 * @param SearchRequest $request
//	 *
//	 * @throws ConfigurationException
//	 * @throws Exception
//	 */
//	public function searchDocuments(
//		Client $client, IFullTextSearchProvider $provider, DocumentAccess $access,
//		SearchRequest $request
//	) {
//		try {
//			$query = $this->searchMappingService->generateSearchQuery($provider, $access, $request);
//		} catch (SearchQueryGenerationException $e) {
//			return null;
//		}
//
//		try {
//			$result = $client->search($query['params']);
//		} catch (Exception $e) {
//			$this->miscService->log(
//				'debug - request: ' . json_encode($request) . '   - query: ' . json_encode($query)
//			);
//			throw $e;
//		}
//
//		$searchResult = $this->generateSearchResultFromResult($result);
//
//		foreach ($result['hits']['hits'] as $entry) {
//			$searchResult->addDocument($this->parseSearchEntry($entry, $access->getViewerId()));
//		}
//	}

	/**
	 * @param Client $client
	 * @param SearchResult $searchResult
	 * @param DocumentAccess $access
	 *
	 * @throws Exception
	 */
	public function searchRequest(Client $client, SearchResult $searchResult, DocumentAccess $access
	) {
		try {
			$query = $this->searchMappingService->generateSearchQuery(
				$searchResult->getRequest(), $access, $searchResult->getProvider()
			);
		} catch (SearchQueryGenerationException $e) {
			return;
		}

		try {
			$result = $client->search($query['params']);
		} catch (Exception $e) {
			$this->miscService->log(
				'debug - request: ' . json_encode($searchResult->getRequest()) . '   - query: '
				. json_encode($query)
			);
			throw $e;
		}

		$this->updateSearchResult($searchResult, $result);

		foreach ($result['hits']['hits'] as $entry) {
			$searchResult->addDocument($this->parseSearchEntry($entry, $access->getViewerId()));
		}
	}
//	/**
//	 * @param Client $client
//	 * @param IFullTextSearchProvider $provider
//	 * @param DocumentAccess $access
//	 * @param SearchResult $result
//	 *
//	 * @return SearchResult
//	 * @throws ConfigurationException
//	 */
//	public function fillSearchResult(
//		Client $client, IFullTextSearchProvider $provider, DocumentAccess $access,
//		SearchResult $searchResult
//	) {
//		try {
//			$query = $this->searchMappingService->generateSearchQuery(
//				$provider, $access, $searchResult->getRequest()
//			);
//		} catch (SearchQueryGenerationException $e) {
//			return null;
//		}
//
//		try {
//			$result = $client->search($query['params']);
//		} catch (Exception $e) {
//			$this->miscService->log(
//				'debug - request: ' . json_encode($searchResult->getRequest()) . '   - query: '
//				. json_encode($query)
//			);
//			throw $e;
//		}
//
//		$this->updateSearchResult($searchResult, $result);
//
//		foreach ($result['hits']['hits'] as $entry) {
//			$searchResult->addDocument($this->parseSearchEntry($entry, $access->getViewerId()));
//		}
//
//		return $searchResult;
//	}


	/**
	 * @param Client $client
	 * @param string $providerId
	 * @param string $documentId
	 *
	 * @return IndexDocument
	 * @throws ConfigurationException
	 */
	public function getDocument(Client $client, $providerId, $documentId) {
		$query = $this->searchMappingService->getDocumentQuery($providerId, $documentId);
		$result = $client->get($query);

		$access = new DocumentAccess($result['_source']['owner']);
		$access->setUsers($result['_source']['users']);
		$access->setGroups($result['_source']['groups']);
		$access->setCircles($result['_source']['circles']);

		$index = new IndexDocument($providerId, $documentId);
		$index->setAccess($access);
		$index->setMetaTags($result['_source']['metatags']);
		$index->setSubTags($result['_source']['subtags']);
		$index->setTags($result['_source']['tags']);
		$index->setMore($result['_source']['more']);
//		$index->setInfo($result['_source']['info']);
		$index->setHash($result['_source']['hash']);
		$index->setSource($result['_source']['source']);
		$index->setTitle($result['_source']['title']);
		$index->setParts($result['_source']['parts']);
		$index->setContent($result['_source']['content']);

		return $index;
	}


	/**
	 * @param SearchResult $searchResult
	 * @param array $result
	 */
	private function updateSearchResult(SearchResult $searchResult, $result) {
		$searchResult->setRawResult(json_encode($result));

		$searchResult->setTotal($result['hits']['total']);
		$searchResult->setMaxScore($result['hits']['max_score']);
		$searchResult->setTime($result['took']);
		$searchResult->setTimedOut($result['timed_out']);
	}


	/**
	 * @param array $entry
	 * @param string $viewerId
	 *
	 * @return IndexDocument
	 */
	private function parseSearchEntry($entry, $viewerId) {
		$access = new DocumentAccess();
		$access->setViewerId($viewerId);

		list($providerId, $documentId) = explode(':', $entry['_id'], 2);
		$document = new IndexDocument($providerId, $documentId);
		$document->setAccess($access);
		$document->setExcerpts(
			(array_key_exists('highlight', $entry)) ? $entry['highlight']['content'] : []
		);
		$document->setHash(MiscService::get($entry['_source'], 'hash'));
		$document->setScore($entry['_score']);
		$document->setSource(MiscService::get($entry['_source'], 'source'));
		$document->setTitle(MiscService::get($entry['_source'], 'title'));

		return $document;
	}


}
