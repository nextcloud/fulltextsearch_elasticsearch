<?php

declare(strict_types=1);

/**
 * FullTextSearch_OpenSearch - Use OpenSearch to index the content of your nextcloud
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

namespace OCA\FullTextSearch_OpenSearch\Service;

use OCA\FullTextSearch_OpenSearch\Exceptions\AccessIsEmptyException;
use OCA\FullTextSearch_OpenSearch\Exceptions\ConfigurationException;
use OCA\FullTextSearch_OpenSearch\Tools\Traits\TArrayTools;
use OCA\FullTextSearch_OpenSearch\Vendor\Http\Client\Exception;
use OCA\FullTextSearch_OpenSearch\Vendor\OpenSearch\Client;
use OCP\FullTextSearch\Model\IIndex;
use OCP\FullTextSearch\Model\IIndexDocument;
use Psr\Log\LoggerInterface;

class IndexService {

	use TArrayTools;

	public function __construct(
		private IndexMappingService $indexMappingService,
		private LoggerInterface $logger,
	) {
	}


	/**
	 * @param Client $client
	 *
	 * @return bool
	 * @throws ConfigurationException
	 */
	public function testIndex(Client $client): bool {
		$map = $this->indexMappingService->generateGlobalMap(false);
		$map['client'] = [
			'verbose' => true
		];

		$result = $client->indices()
			->exists($map);

		return $result;
	}


	/**
	 * @param Client $client
	 *
	 * @throws ConfigurationException
	 */
	public function initializeIndex(Client $client): void {
		try {
			if ($client->indices()
				->exists($this->indexMappingService->generateGlobalMap(false))) {
				return;
			}
		} catch (Exception $e) {
			$this->logger->warning($e->getMessage(), ['exception' => $e]);
		}

		try {
			$client->indices()
				->create($this->indexMappingService->generateGlobalMap());
		} catch (Exception $e) {
			$this->logger->notice('reset index all', ['exception' => $e]);
			$this->resetIndexAll($client);
		}

		try {
			$client->ingest()
				->putPipeline($this->indexMappingService->generateGlobalIngest());
		} catch (Exception $e) {
			$this->logger->notice('reset index all', ['exception' => $e]);
			$this->resetIndexAll($client);
		}
	}


	/**
	 * @param Client $client
	 * @param string $providerId
	 *
	 * @throws ConfigurationException
	 */
	public function resetIndex(Client $client, string $providerId): void {
		try {
			$client->deleteByQuery($this->indexMappingService->generateDeleteQuery($providerId));
		} catch (Exception $e) {
			$this->logger->notice('reset index all', ['exception' => $e]);
		}
	}


	/**
	 * @param Client $client
	 *
	 * @throws ConfigurationException
	 */
	public function resetIndexAll(Client $client): void {
		try {
			$client->ingest()
				->deletePipeline($this->indexMappingService->generateGlobalIngest(false));
		} catch (Exception $e) {
			$this->logger->warning($e->getMessage(), ['exception' => $e]);
		}

		try {
			$client->indices()
				->delete($this->indexMappingService->generateGlobalMap(false));
		} catch (Exception $e) {
			$this->logger->warning($e->getMessage(), ['exception' => $e]);
		}
	}


	/**
	 * @param Client $client
	 * @param IIndex $index
	 *
	 * @throws ConfigurationException
	 */
	public function deleteIndex(Client $client, IIndex $index): void {
		$this->indexMappingService->indexDocumentRemove(
			$client,
			$index->getProviderId(),
			$index->getDocumentId()
		);
	}


	/**
	 * @param Client $client
	 * @param IIndexDocument $document
	 *
	 * @return array
	 * @throws ConfigurationException
	 * @throws AccessIsEmptyException
	 */
	public function indexDocument(Client $client, IIndexDocument $document): array {
		$result = [];
		$index = $document->getIndex();
		if ($index->isStatus(IIndex::INDEX_REMOVE)) {
			$this->indexMappingService->indexDocumentRemove(
				$client, $document->getProviderId(), $document->getId()
			);
		} elseif ($index->isStatus(IIndex::INDEX_OK) && !$index->isStatus(IIndex::INDEX_CONTENT)
				   && !$index->isStatus(IIndex::INDEX_META)) {
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
	public function parseIndexResult(IIndex $index, array $result): IIndex {
		$index->setLastIndex();

		if (array_key_exists('exception', $result)) {
			$index->setStatus(IIndex::INDEX_FAILED);
			$index->addError(
				$this->get('message', $result, $result['exception']),
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
}
