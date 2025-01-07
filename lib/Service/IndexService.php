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

    /**
     * @param IndexMappingService $indexMappingService An instance of the IndexMappingService to handle index mappings.
     * @param LoggerInterface $logger An instance of the LoggerInterface for handling logging.
     */
    public function __construct(
        /**
         *
         */ private IndexMappingService $indexMappingService,
            private LoggerInterface     $logger,
	) {
	}


    /**
     * Tests the existence of a specified index using the provided client.
     *
     * @param Client $client The client instance used to check the index existence.
     *
     * @return bool Returns true if the index exists, false otherwise.
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
     * Initializes the index for the given client by checking if the index exists, creating it if necessary,
     * and setting up the ingest pipeline. If any exceptions occur during these operations, they are logged,
     * and a reset operation is initiated.
     *
     * @param Client $client The client instance used to interact with the index and ingest pipeline.
     * @return void
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
     * Resets the index for the specified provider by executing a delete query using the given client.
     * If an exception occurs during the operation, it is logged.
     *
     * @param Client $client The client instance used to interact with the index.
     * @param string $providerId The identifier of the provider for which the index should be reset.
     * @return void
     */
	public function resetIndex(Client $client, string $providerId): void {
		try {
			$client->deleteByQuery($this->indexMappingService->generateDeleteQuery($providerId));
		} catch (Exception $e) {
			$this->logger->notice('reset index all', ['exception' => $e]);
		}
	}


    /**
     * Resets all indexes for the given client by deleting the ingest pipeline and the index mapping.
     * Logs any exceptions encountered during these operations.
     *
     * @param Client $client The client instance used to delete the ingest pipeline and index mapping.
     * @return void
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
     * Deletes an index document using the specified client and index details.
     * This method removes the document associated with the provided index's provider and document IDs.
     *
     * @param Client $client The client instance used to interact with the index.
     * @param IIndex $index The index interface containing the provider and document IDs for the document to be removed.
     * @return void
     */
	public function deleteIndex(Client $client, IIndex $index): void {
		$this->indexMappingService->indexDocumentRemove(
			$client,
			$index->getProviderId(),
			$index->getDocumentId()
		);
	}


    /**
     * Indexes a document in the specified index based on its current status. If the document's status
     * is set to remove, it attempts to remove the document from the index. If the status indicates
     * the document is valid and neither content nor metadata has been set, it updates the document
     * in the index. Otherwise, it treats the document as new and adds it to the index.
     *
     * @param Client $client The client instance used to perform indexing operations on the document.
     * @param IIndexDocument $document The document to be indexed, containing its status, provider ID, and other data.
     * @return array The result of the indexing operation, detailing success or failure of the operation.
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
     * Parses the result of an indexing operation and updates the provided index object
     * with relevant status and error information based on the result.
     *
     * @param IIndex $index The index object to be updated based on the result.
     * @param array $result The result data of the indexing operation.
     * @return IIndex The updated index object with modified status or error attributes.
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
