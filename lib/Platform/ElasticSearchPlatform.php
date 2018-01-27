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
use Elasticsearch\Common\Exceptions\Curl\CouldNotConnectToHost;
use Elasticsearch\Common\Exceptions\MaxRetriesException;
use Exception;
use OCA\FullTextSearch\Exceptions\InterruptException;
use OCA\FullTextSearch\Exceptions\TickDoesNotExistException;
use OCA\FullTextSearch\IFullTextSearchPlatform;
use OCA\FullTextSearch\IFullTextSearchProvider;
use OCA\FullTextSearch\Model\DocumentAccess;
use OCA\FullTextSearch\Model\Index;
use OCA\FullTextSearch\Model\IndexDocument;
use OCA\FullTextSearch\Model\Runner;
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
		return $this->configService->getAppValue('installed_version');
	}

//	public function getClient() {
//		return $this->client;
//	}


	/**
	 * @param Runner $runner
	 */
	public function setRunner(Runner $runner) {
		$this->runner = $runner;
	}

	/**
	 * @param $action
	 *
	 * @throws InterruptException
	 * @throws TickDoesNotExistException
	 */
	private function updateRunner($action) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->update($action);
	}


	/**
	 * @param $line
	 */
	private function outputRunner($line) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->output($line);
	}


	/**
	 * Called when loading the platform.
	 *
	 * Loading some container and connect to ElasticSearch.
	 *
	 * @throws ConfigurationException
	 * @throws QueryException
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
	 */
	public function testPlatform() {
	}


	/**
	 * called before any index
	 *
	 * We create a general index.
	 *
	 * @param IFullTextSearchProvider $provider
	 *
	 * @throws ConfigurationException
	 */
	public function initializeIndex(IFullTextSearchProvider $provider) {
		$this->indexService->initializeIndex($this->client);

		$provider->onInitializingIndex($this);
	}


	/**
	 * resetIndex();
	 *
	 * Called when admin wants to remove an index specific to a $provider.
	 * $provider can be null, meaning a reset of the whole index.
	 *
	 * @param IFullTextSearchProvider|null $provider
	 *
	 * @throws ConfigurationException
	 */
	public function resetIndex($provider) {

		if ($provider instanceof IFullTextSearchProvider) {
			// TODO: need to specify the map to remove
			// TODO: need to remove entries with type=providerId
			$provider->onResettingIndex($this);
		}

		$this->indexService->resetIndex($this->client);
	}


	/**
	 * {@inheritdoc}
	 */
	public function indexDocuments(IFullTextSearchProvider $provider, $documents) {
		$indexes = [];
		foreach ($documents as $document) {
			$index = $this->indexDocument($provider, $document);
			if ($index !== null) {
				$indexes[] = $index;
			}
		}

		return $indexes;
	}


	/**
	 * {@inheritdoc}
	 */
	public function indexDocument(IFullTextSearchProvider $provider, IndexDocument $document) {

		$this->updateRunner('indexDocument');
		$this->outputRunner(' . Indexing: ' . $document->getTitle());

		try {
			$result = $this->indexService->indexDocument($this, $this->client, $provider, $document);
			$this->outputRunner('  result: ' . json_encode($result));

			return $this->indexService->parseIndexResult($document->getIndex(), $result);
		} catch (Exception $e) {
			return $this->indexDocumentError($provider, $document, $e);
		}

	}


	/**
	 * @param IFullTextSearchProvider $provider
	 * @param IndexDocument $document
	 * @param Exception $e
	 *
	 * @return Index
	 * @throws ConfigurationException
	 * @throws AccessIsEmptyException
	 */
	private function indexDocumentError(
		IFullTextSearchProvider $provider, IndexDocument $document, Exception $e
	) {
		$message = [
			'exception' => get_class($e),
			'message'   => $e->getMessage()
		];

		$document->setContent(null);
		$index = $document->getIndex();
		$index->unsetStatus(Index::INDEX_CONTENT);
		$index->setMessage(json_encode($message));

		$result = $this->indexService->indexDocument($this, $this->client, $provider, $document);
		$this->outputRunner('  result with no content: ' . json_encode($result));

		return $this->indexService->parseIndexResult($document->getIndex(), $result);
	}


	/**
	 * {@inheritdoc}
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
	 */
	public function searchDocuments(IFullTextSearchProvider $provider, DocumentAccess $access, $request
	) {
		try {
			return $this->searchService->searchDocuments(
				$this, $this->client, $provider, $access, $request
			);
		} catch (ConfigurationException $e) {
			throw $e;
		}
	}


	/**
	 * @param string $host
	 */
	private function connectToElastic($host) {

		try {
			$hosts = [MiscService::noEndSlash($host)];
			$this->client = ClientBuilder::create()
										 ->setHosts($hosts)
										 ->setRetries(2)
										 ->build();

		} catch (CouldNotConnectToHost $e) {
			echo 'CouldNotConnectToHost';
			$previous = $e->getPrevious();
			if ($previous instanceof MaxRetriesException) {
				echo "Max retries!";
			}
		} catch (Exception $e) {
			echo ' ElasticSearchPlatform::load() Exception --- ' . $e->getMessage() . "\n";
		}
	}


}