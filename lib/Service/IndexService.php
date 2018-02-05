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
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use OCA\FullTextSearch\IFullTextSearchPlatform;
use OCA\FullTextSearch\IFullTextSearchProvider;
use OCA\FullTextSearch\Model\Index;
use OCA\FullTextSearch\Model\IndexDocument;
use OCA\FullTextSearch_ElasticSearch\Exceptions\AccessIsEmptyException;
use OCA\FullTextSearch_ElasticSearch\Exceptions\ConfigurationException;

class IndexService {


	/** @var IndexMappingService */
	private $indexMappingService;

	/** @var MiscService */
	private $miscService;


	/**
	 * IndexService constructor.
	 *
	 * @param IndexMappingService $indexMappingService
	 * @param MiscService $miscService
	 */
	public function __construct(
		IndexMappingService $indexMappingService, MiscService $miscService
	) {
		$this->indexMappingService = $indexMappingService;
		$this->miscService = $miscService;
	}


	/**
	 * @param Client $client
	 *
	 * @throws ConfigurationException
	 */
	public function initializeIndex(Client $client) {
		try {
			$result = $client->indices()
							 ->exists($this->indexMappingService->generateGlobalMap(false));
		} catch (BadRequest400Exception $e) {
			throw new ConfigurationException(
				'Check your user/password and the index assigned to that cloud'
			);
		}

		try {
			if (!$result) {
				$client->indices()
					   ->create($this->indexMappingService->generateGlobalMap());
				$client->ingest()
					   ->putPipeline($this->indexMappingService->generateGlobalIngest());
			}
		} catch (BadRequest400Exception $e) {
			$this->resetIndex($client);
			$this->parseBadRequest400($e);
		}
	}


	/**
	 * @param Client $client
	 *
	 * @throws ConfigurationException
	 */
	public function resetIndex(Client $client) {
		try {
			$client->ingest()
				   ->deletePipeline($this->indexMappingService->generateGlobalIngest(false));
		} catch (Missing404Exception $e) {
			/* 404Exception will means that the mapping for that provider does not exist */
		} catch (BadRequest400Exception $e) {
			throw new ConfigurationException(
				'Check your user/password and the index assigned to that cloud'
			);
		}

		try {
			$client->indices()
				   ->delete($this->indexMappingService->generateGlobalMap(false));
		} catch (Missing404Exception $e) {
			/* 404Exception will means that the mapping for that provider does not exist */
		}
	}


	/**
	 * @param Client $client
	 * @param Index[] $indexes
	 *
	 * @throws ConfigurationException
	 */
	public function deleteIndexes($client, $indexes) {
		foreach ($indexes as $index) {
			$this->indexMappingService->indexDocumentRemove(
				$client, $index->getProviderId(), $index->getDocumentId()
			);
		}
	}


	/**
	 * @param IFullTextSearchPlatform $platform
	 * @param Client $client
	 * @param IFullTextSearchProvider $provider
	 * @param IndexDocument $document
	 *
	 * @return array
	 * @throws ConfigurationException
	 * @throws AccessIsEmptyException
	 */
	public function indexDocument(
		IFullTextSearchPlatform $platform, Client $client, IFullTextSearchProvider $provider,
		IndexDocument $document
	) {
		$index = $document->getIndex();
		if ($index->isStatus(Index::INDEX_REMOVE)) {
			$result = $this->indexMappingService->indexDocumentRemove(
				$client, $provider->getId(), $document->getId()
			);
		} else if ($index->isStatus(Index::INDEX_OK) && !$index->isStatus(Index::INDEX_CONTENT)) {
			$result = $this->indexMappingService->indexDocumentUpdate(
				$client, $provider, $document, $platform
			);
		} else {
			$result = $this->indexMappingService->indexDocumentNew(
				$client, $provider, $document, $platform
			);
		}

		return $result;
	}


	/**
	 * @param Index $index
	 * @param array $result
	 *
	 * @return Index
	 */
	public function parseIndexResult(Index $index, array $result) {

		$index->setLastIndex();

		if (array_key_exists('exception', $result)) {
			$index->setStatus(Index::INDEX_FAILED);
			$index->incrementError();
			$index->setMessage(json_encode($result));

			return $index;
		}

		// TODO: parse result
		$index->setStatus(Index::INDEX_DONE);

		return $index;
	}


	/**
	 * @param BadRequest400Exception $e
	 *
	 * @throws ConfigurationException
	 */
	private function parseBadRequest400(BadRequest400Exception $e) {

		$data = json_decode($e->getMessage(), true);
		$rootCause = $data['error']['root_cause'][0];

		if ($rootCause['type'] === 'parse_exception') {
			throw new ConfigurationException(
				'please add ingest-attachment plugin to elasticsearch'
			);
		}

		throw new ConfigurationException($rootCause['reason']);
	}

}
