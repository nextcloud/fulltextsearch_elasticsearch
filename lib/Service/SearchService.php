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
use OCA\FullTextSearch\IFullTextSearchPlatform;
use OCA\FullTextSearch\IFullTextSearchProvider;
use OCA\FullTextSearch\Model\DocumentAccess;
use OCA\FullTextSearch\Model\IndexDocument;
use OCA\FullTextSearch\Model\SearchRequest;
use OCA\FullTextSearch\Model\SearchResult;
use OCA\FullTextSearch_ElasticSearch\Exceptions\ConfigurationException;

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


	/**
	 * @param IFullTextSearchPlatform $source
	 * @param Client $client
	 * @param IFullTextSearchProvider $provider
	 * @param DocumentAccess $access
	 * @param SearchRequest $request
	 *
	 * @return SearchResult
	 * @throws ConfigurationException
	 */
	public function searchDocuments(
		IFullTextSearchPlatform $source, Client $client, IFullTextSearchProvider $provider,
		DocumentAccess $access, SearchRequest $request
	) {

		$query = $this->searchMappingService->generateSearchQuery($provider, $access, $request);
		$provider->onSearchingQuery($source, $request, $query);

		$result = $client->search($query['params']);
		$searchResult = $this->generateSearchResultFromResult($result);
		$searchResult->setProvider($provider);

		foreach ($result['hits']['hits'] as $entry) {
			$searchResult->addDocument($this->parseSearchEntry($entry, $access->getViewerId()));
		}

		return $searchResult;
	}


	/**
	 * @param array $result
	 *
	 * @return SearchResult
	 */
	private function generateSearchResultFromResult($result) {
		$searchResult = new SearchResult();
		$searchResult->setRawResult(json_encode($result));

		$searchResult->setTotal($result['hits']['total']);
		$searchResult->setMaxScore($result['hits']['max_score']);
		$searchResult->setTime($result['took']);
		$searchResult->setTimedOut($result['timed_out']);

		return $searchResult;
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
		$document->setScore($entry['_score']);
		$document->setSource(MiscService::get($entry['_source'], 'source'));
		$document->setTitle(MiscService::get($entry['_source'], 'title'));

		return $document;
	}


}
