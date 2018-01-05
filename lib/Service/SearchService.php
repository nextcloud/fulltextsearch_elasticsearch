<?php
/**
 * FullNextSearch_ElasticSearch - Index with ElasticSearch
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
 */

namespace OCA\FullNextSearch_ElasticSearch\Service;

use Elasticsearch\Client;
use OCA\FullNextSearch\INextSearchPlatform;
use OCA\FullNextSearch\INextSearchProvider;
use OCA\FullNextSearch\Model\DocumentAccess;
use OCA\FullNextSearch\Model\IndexDocument;
use OCA\FullNextSearch\Model\SearchRequest;
use OCA\FullNextSearch\Model\SearchResult;
use OCA\FullNextSearch_ElasticSearch\Exceptions\ConfigurationException;

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
	 * @param INextSearchPlatform $source
	 * @param Client $client
	 * @param INextSearchProvider $provider
	 * @param DocumentAccess $access
	 * @param SearchRequest $request
	 *
	 * @return SearchResult
	 * @throws ConfigurationException
	 */
	public function searchDocuments(
		INextSearchPlatform $source, Client $client, INextSearchProvider $provider,
		DocumentAccess $access, SearchRequest $request
	) {

		$query = $this->searchMappingService->generateSearchQuery($provider, $access, $request);
		$provider->onSearchingQuery($source, $request, $query);

		$result = $client->search($query['params']);
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


}
