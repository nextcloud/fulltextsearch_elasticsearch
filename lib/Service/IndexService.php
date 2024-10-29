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
use OCA\FullTextSearch_Elasticsearch\Exceptions\AccessIsEmptyException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\ConfigurationException;
use OCA\FullTextSearch_Elasticsearch\Tools\Traits\TArrayTools;
use OCP\FullTextSearch\Model\IIndex;
use OCP\FullTextSearch\Model\IIndexDocument;
use Psr\Log\LoggerInterface;

class IndexService {

	use TArrayTools;

	public function __construct(
		private IndexMappingService $indexMappingService,
		private LoggerInterface $logger
	) {
	}


	/**
	 * @param Client $client
	 *
	 * @return bool
	 * @throws ClientResponseException
	 * @throws ConfigurationException
	 * @throws MissingParameterException
	 * @throws ServerResponseException
	 */
	public function testIndex(Client $client): bool {
		$map = $this->indexMappingService->generateGlobalMap(false);
		$map['client'] = [
			'verbose' => true
		];

		$result = $client->indices()
						 ->exists($map);

		return $result->asBool();
	}


	/**
	 * @param Client $client
	 *
	 * @throws ConfigurationException
	 * @throws MissingParameterException
	 * @throws ServerResponseException
	 */
	public function initializeIndex(Client $client): void {
		try {
			if ($client->indices()
					   ->exists($this->indexMappingService->generateGlobalMap(false))
					   ->asBool()) {
				return;
			}
		} catch (ClientResponseException $e) {
			$this->logger->warning($e->getMessage(), ['exception' => $e]);
		}

		try {
			$client->indices()
				   ->create($this->indexMappingService->generateGlobalMap());
		} catch (ClientResponseException $e) {
			$this->logger->notice('reset index all', ['exception' => $e]);
			$this->resetIndexAll($client);
		}

		try {
			$client->ingest()
				   ->putPipeline($this->indexMappingService->generateGlobalIngest());
		} catch (ClientResponseException $e) {
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
		} catch (ClientResponseException $e) {
			$this->logger->notice('reset index all', ['exception' => $e]);
		}
	}


	/**
	 * @param Client $client
	 *
	 * @throws ConfigurationException
	 * @throws MissingParameterException
	 * @throws ServerResponseException
	 */
	public function resetIndexAll(Client $client): void {
		try {
			$client->ingest()
				   ->deletePipeline($this->indexMappingService->generateGlobalIngest(false));
		} catch (ClientResponseException $e) {
			$this->logger->warning($e->getMessage(), ['exception' => $e]);
		}

		try {
			$client->indices()
				   ->delete($this->indexMappingService->generateGlobalMap(false));
		} catch (ClientResponseException $e) {
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
		} else if ($index->isStatus(IIndex::INDEX_OK) && !$index->isStatus(IIndex::INDEX_CONTENT)
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
