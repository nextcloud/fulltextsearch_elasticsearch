<?php
declare(strict_types=1);


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
use OCA\FullTextSearch_ElasticSearch\Exceptions\AccessIsEmptyException;
use OCA\FullTextSearch_ElasticSearch\Exceptions\ConfigurationException;
use OCP\FullTextSearch\IFullTextSearchProvider;
use OCP\FullTextSearch\Model\IIndex;
use OCP\FullTextSearch\Model\IndexDocument;

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
	 * @return bool
	 * @throws ConfigurationException
	 */
	public function testIndex(Client $client) {

		$map = $this->indexMappingService->generateGlobalMap(false);
		$map['client'] = [
			'verbose' => true
		];

		return $client->indices()
					  ->exists($map);
	}

	/**
	 * @param Client $client
	 *
	 * @throws ConfigurationException
	 * @throws BadRequest400Exception
	 */
	public function initializeIndex(Client $client) {
		try {
			if ($client->indices()
					   ->exists($this->indexMappingService->generateGlobalMap(false))) {
				return;
			}
		} catch (BadRequest400Exception $e) {
			$this->parseBadRequest400($e);
		}

		try {
			$client->indices()
				   ->create($this->indexMappingService->generateGlobalMap());
			$client->ingest()
				   ->putPipeline($this->indexMappingService->generateGlobalIngest());
		} catch (BadRequest400Exception $e) {
			$this->resetIndexAll($client);
			$this->parseBadRequest400($e);
		}
	}


	/**
	 * @param Client $client
	 * @param $providerId
	 *
	 * @throws ConfigurationException
	 */
	public function resetIndex(Client $client, $providerId) {
		try {
			$client->deleteByQuery($this->indexMappingService->generateDeleteQuery($providerId));
		} catch (Missing404Exception $e) {
			/** we do nothin' */
		}
	}


	/**
	 * @param Client $client
	 *
	 * @throws ConfigurationException
	 */
	public function resetIndexAll(Client $client) {
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
	 * @param IIndex[] $indexes
	 *
	 * @throws ConfigurationException
	 */
	public function deleteIndexes(Client $client, array $indexes) {
		foreach ($indexes as $index) {
			$this->indexMappingService->indexDocumentRemove(
				$client, $index->getProviderId(), $index->getDocumentId()
			);
		}
	}


	/**
	 * @param Client $client
	 * @param IFullTextSearchProvider $provider
	 * @param IndexDocument $document
	 *
	 * @return array
	 * @throws ConfigurationException
	 * @throws AccessIsEmptyException
	 */
	public function indexDocument(Client $client, IndexDocument $document) {
		$result = [];
		$index = $document->getIndex();
		if ($index->isStatus(IIndex::INDEX_REMOVE)) {
			$this->indexMappingService->indexDocumentRemove(
				$client, $document->getProviderId(), $document->getId()
			);
		} else if ($index->isStatus(IIndex::INDEX_OK) && !$index->isStatus(IIndex::INDEX_CONTENT)) {
			$result = $this->indexMappingService->indexDocumentUpdate($client, $document);
		} else {
			$result = $this->indexMappingService->indexDocumentNew($client, $document);
		}

		return $result;
	}


	/**
	 * @param IIndex $index
	 * @param array $result
	 *
	 * @return IIndex
	 */
	public function parseIndexResult(IIndex $index, array $result) {

		$index->setLastIndex();

		if (array_key_exists('exception', $result)) {
			$index->setStatus(IIndex::INDEX_FAILED);
			$index->addError(
				$this->miscService->get($result, 'message', $result['exception']),
				'',
				IIndex::ERROR_SEV_3
			);

			return $index;
		}

		// TODO: parse result
		if ($index->getErrorCount() === 0) {
			$index->setStatus(IIndex::INDEX_DONE);
		}

		return $index;
	}


	/**
	 * @param BadRequest400Exception $e
	 *
	 * @throws ConfigurationException
	 * @throws BadRequest400Exception
	 */
	private function parseBadRequest400(BadRequest400Exception $e) {

		if ($e->getMessage() === '') {
			throw new ConfigurationException(
				'Check your user/password and the index assigned to that cloud'
			);
		}


		$error = json_decode($e->getMessage(), true)['error'];

		if ($error['type'] === 'parse_exception') {
			if ($error['reason'] === 'No processor type exists with name [attachment]') {
				throw new ConfigurationException(
					'please add ingest-attachment plugin to elasticsearch'
				);
			}
		}

		throw $e;
	}

}
