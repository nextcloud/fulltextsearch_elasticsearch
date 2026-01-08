<?php
declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Platform;


use Exception;
use InvalidArgumentException;
use OCA\FullTextSearch_Elasticsearch\ConfigLexicon;
use OCA\FullTextSearch_Elasticsearch\Exceptions\AccessIsEmptyException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\ClientException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\ConfigurationException;
use OCA\FullTextSearch_Elasticsearch\Service\ConfigService;
use OCA\FullTextSearch_Elasticsearch\Service\IndexService;
use OCA\FullTextSearch_Elasticsearch\Service\SearchService;
use OCA\FullTextSearch_Elasticsearch\Tools\Traits\TArrayTools;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Client;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\ClientBuilder;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\ClientResponseException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\Exception\NoNodeAvailableException;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Elastic\Elasticsearch\Client as Client8;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Elastic\Elasticsearch\ClientBuilder as ClientBuilder8;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\Exception\NoNodeAvailableException as NoNodeAvailableException8;
use OCP\AppFramework\Services\IAppConfig;
use OCP\FullTextSearch\Exceptions\PlatformTemporaryException;
use OCP\FullTextSearch\IFullTextSearchPlatform;
use OCP\FullTextSearch\Model\IDocumentAccess;
use OCP\FullTextSearch\Model\IIndex;
use OCP\FullTextSearch\Model\IIndexDocument;
use OCP\FullTextSearch\Model\IRunner;
use OCP\FullTextSearch\Model\ISearchResult;
use Psr\Log\LoggerInterface;

/**
 * Class ElasticSearchPlatform
 *
 * @package OCA\FullTextSearch_Elasticsearch\Platform
 */
class ElasticSearchPlatform implements IFullTextSearchPlatform {


	use TArrayTools;

	private null|Client|Client8 $client = null;
	private ?IRunner $runner = null;
    private bool $downgradingES = false;

	public function __construct(
		private readonly IAppConfig $appConfig,
		private ConfigService $configService,
		private IndexService $indexService,
		private SearchService $searchService,
		private LoggerInterface $logger,
	) {
	}


	/**
	 * return a unique Id of the platform.
	 */
	public function getId(): string {
		return 'elastic_search';
	}


	/**
	 * return a unique Id of the platform.
	 */
	public function getName(): string {
		return 'Elasticsearch';
	}


	/**
	 * @return array
	 * @throws ConfigurationException
	 */
	public function getConfiguration(): array {
		$result = $this->configService->getConfig();

		$sanitizedHosts = [];
		$hosts = $this->getElasticHost();
		foreach ($hosts as $host) {
			$parsedHost = parse_url($host);
			$safeHost = $parsedHost['scheme'] . '://';
			if (array_key_exists('user', $parsedHost)) {
				$safeHost .= $parsedHost['user'] . ':' . '********' . '@';
			}
			$safeHost .= $parsedHost['host'];
			$safeHost .= ':' . $parsedHost['port'];

			$sanitizedHosts[] = $safeHost;
		}

		$result[ConfigLexicon::ELASTIC_HOST] = $sanitizedHosts;

		return $result;
	}


	/**
	 * @return array
	 * @throws ConfigurationException
	 */
	public function getElasticHost(): array {
		$strHost = $this->appConfig->getAppValueString(ConfigLexicon::ELASTIC_HOST);
		if ($strHost === '') {
			throw new ConfigurationException('Your ElasticSearchPlatform is not configured properly');
		}

		return array_map('trim', explode(',', $strHost));
	}


	/**
	 * @param IRunner $runner
	 */
	public function setRunner(IRunner $runner) {
		$this->runner = $runner;
	}


	/**
	 * Called when loading the platform.
	 *
	 * Loading some container and connect to ElasticSearch.
	 *
	 * @throws ConfigurationException
	 * @throws Exception
	 */
	public function loadPlatform() {
		$this->connectToElastic($this->getElasticHost());
	}


	/**
	 * not used yet.
	 *
	 * @return bool
	 */
	public function testPlatform(): bool {
		$ping = $this->getClient()->ping();
		return $ping->asBool();
	}


	/**
	 * called before any index
	 *
	 * We create a general index.
	 *
	 * @throws ConfigurationException
	 * @throws BadRequest400Exception
	 */
	public function initializeIndex() {
		$this->indexService->initializeIndex($this->getClient());
	}


	/**
	 * resetIndex();
	 *
	 * Called when admin wants to remove an index specific to a $provider.
	 * $provider can be null, meaning a reset of the whole index.
	 *
	 * @param string $providerId
	 *
	 * @throws ConfigurationException
	 */
	public function resetIndex(string $providerId) {
		if ($providerId === 'all') {
			$this->indexService->resetIndexAll($this->getClient());
		} else {
			$this->indexService->resetIndex($this->getClient(), $providerId);
		}
	}


	/**
	 * @param IIndexDocument $document
	 *
	 * @return IIndex
	 */
	public function indexDocument(IIndexDocument $document): IIndex {
		$document->initHash();
		try {
			$result = $this->indexService->indexDocument($this->getClient(), $document);
			$index = $this->indexService->parseIndexResult($document->getIndex(), $result);

			$this->updateNewIndexResult(
				$document->getIndex(), json_encode($result), 'ok',
				IRunner::RESULT_TYPE_SUCCESS
			);

			return $index;
		} catch (NoNodeAvailableException|NoNodeAvailableException8) {
			throw new PlatformTemporaryException();
		} catch (Exception $e) {
			$this->manageIndexErrorException($document, $e);
		}

		try {
			$result = $this->indexDocumentError($document, $e);
			$index = $this->indexService->parseIndexResult($document->getIndex(), $result);

			$this->updateNewIndexResult(
				$document->getIndex(), json_encode($result), 'ok',
				IRunner::RESULT_TYPE_WARNING
			);

			return $index;
		} catch (Exception $e) {
			$this->updateNewIndexResult(
				$document->getIndex(), '', 'fail',
				IRunner::RESULT_TYPE_FAIL
			);
			$this->manageIndexErrorException($document, $e);
		}

		return $document->getIndex();
	}


	/**
	 * @param IIndexDocument $document
	 * @param Exception $e
	 *
	 * @return array
	 * @throws AccessIsEmptyException
	 * @throws ConfigurationException
	 * @throws Exception
	 */
	private function indexDocumentError(IIndexDocument $document, Exception $e): array {

		$this->updateRunnerAction('indexDocumentWithoutContent', true);

		$document->setContent('');
//		$index = $document->getIndex();
//		$index->unsetStatus(Index::INDEX_CONTENT);

		return $this->indexService->indexDocument($this->getClient(), $document);
	}


	/**
	 * @param IIndexDocument $document
	 * @param Exception $e
	 */
	private function manageIndexErrorException(IIndexDocument $document, Exception $e) {
		[$level, $message, $status] = $this->parseIndexErrorException($e);
		switch ($level) {
			case 'error':
				$document->getIndex()
						 ->addError($message, get_class($e), IIndex::ERROR_SEV_3);
				$this->updateNewIndexError(
					$document->getIndex(), $message, get_class($e), IIndex::ERROR_SEV_3
				);
				break;

			case 'notice':
				$this->updateNewIndexResult(
					$document->getIndex(),
					$message,
					$status,
					IRunner::RESULT_TYPE_WARNING
				);
				break;
		}

	}


	/**
	 * @param Exception $e
	 *
	 * @return array
	 */
	private function parseIndexErrorException(Exception $e): array {
		$arr = json_decode($e->getMessage(), true);
		if (!is_array($arr)) {
			return ['error', 'unknown error', ''];
		}

		if (empty($this->getArray('error', $arr))) {
			return ['error', $e->getMessage(), ''];
		}

		try {
			return $this->parseCausedBy($arr['error']);
		} catch (InvalidArgumentException $e) {
		}

		$cause = $this->getArray('error.root_cause', $arr);
		if (!empty($cause) && $this->get('reason', $cause[0]) !== '') {
			return ['error', $this->get('reason', $cause[0]), $this->get('type', $cause[0])];
		}

		return ['error', $e->getMessage(), ''];
	}

	/**
	 * @throws InvalidArgumentException
	 */
	private function parseCausedBy(array $error): array {
		$causedBy = $this->getArray('caused_by.caused_by', $error);
		if (empty($causedBy)) {
			$causedBy = $this->getArray('caused_by', $error);
		}

		if (empty($causedBy)) {
			if ($this->get('reason', $error) === '') {
				throw new InvalidArgumentException('Unable to parse given response structure');
			}

			return ['error', $this->get('reason', $error), $this->get('type', $error)];
		}

		$warnings = [
			'encrypted_document_exception',
			'invalid_password_exception'
		];

		$level = 'error';
		if (in_array($this->get('type', $causedBy), $warnings)) {
			$level = 'notice';
		}

		return [$level, $this->get('reason', $causedBy), $this->get('type', $causedBy)];
	}


	/**
	 * {@inheritdoc}
	 */
	public function deleteIndexes(array $indexes) {
		foreach ($indexes as $index) {
			try {
				$this->indexService->deleteIndex($this->getClient(), $index);
				$this->updateNewIndexResult($index, 'index deleted', 'success', IRunner::RESULT_TYPE_SUCCESS);
			} catch (Exception $e) {
				$this->updateNewIndexResult(
					$index, 'index not deleted', 'issue while deleting index', IRunner::RESULT_TYPE_WARNING
				);
			}
		}
	}


	/**
	 * {@inheritdoc}
	 * @throws Exception
	 */
	public function searchRequest(ISearchResult $result, IDocumentAccess $access) {
		$this->searchService->searchRequest($this->getClient(), $result, $access);
	}


	/**
	 * @param string $providerId
	 * @param string $documentId
	 *
	 * @return IIndexDocument
	 * @throws ConfigurationException
	 */
	public function getDocument(string $providerId, string $documentId): IIndexDocument {
		return $this->searchService->getDocument($this->getClient(), $providerId, $documentId);
	}


	private function cleanHost(string $host): string {
		if ($host === '/') {
			return $host;
		}

		return trim(rtrim($host, '/'));
	}

	/**
	 * @param array $hosts
	 *
	 * @throws Exception
	 */
	private function connectToElastic(array $hosts): void {
		$hosts = array_map([$this, 'cleanHost'], $hosts);
        if ($this->downgradingES) {
            $cb = ClientBuilder8::create();
        } else {
            $cb = ClientBuilder::create();
        }

        $cb->setHosts($hosts)
            ->setRetries(3);

        if ($this->appConfig->getAppValueBool(ConfigLexicon::ELASTIC_LOGGER_ENABLED)) {
			$cb->setLogger($this->logger);
		}

		$cb->setSSLVerification(!$this->appConfig->getAppValueBool(ConfigLexicon::ALLOW_SELF_SIGNED_CERT));
		$this->configureAuthentication($cb, $hosts);

		$this->client = $cb->build();
        $this->confirmESVersion();
	}

    /**
     * if we cannot connect to ES, we try using old lib
     */
    private function confirmESVersion(): void {
        if ($this->downgradingES === true) {
            return;
        }

        try {
            $this->getClient()->info();
        } catch (ClientResponseException) {
            $this->downgradingES = true;
            $this->loadPlatform();
        }
    }


	/**
	 * setBasicAuthentication() on ClientBuilder if available, using list of hosts
	 */
	private function configureAuthentication(ClientBuilder|ClientBuilder8 $cb, array $hosts): void {
		foreach ($hosts as $host) {
			$user = parse_url($host, PHP_URL_USER) ?? '';
			$pass = parse_url($host, PHP_URL_PASS) ?? '';

			if ($user !== '' || $pass !== '') {
				$cb->setBasicAuthentication($user, $pass);
				return;
			}
		}
	}


	/**
	 * @param string $action
	 * @param bool $force
	 *
	 * @throws Exception
	 */
	private function updateRunnerAction(string $action, bool $force = false) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->updateAction($action, $force);
	}


	/**
	 * @param IIndex $index
	 * @param string $message
	 * @param string $exception
	 * @param int $sev
	 */
	private function updateNewIndexError(IIndex $index, string $message, string $exception, int $sev
	) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->newIndexError($index, $message, $exception, $sev);
	}


	/**
	 * @param IIndex $index
	 * @param string $message
	 * @param string $status
	 * @param int $type
	 */
	private function updateNewIndexResult(IIndex $index, string $message, string $status, int $type) {
		if ($this->runner === null) {
			return;
		}

		$this->runner->newIndexResult($index, $message, $status, $type);
	}


	/**
	 * @return Client|Client8
	 * @throws ClientException
	 */
	private function getClient(): Client|Client8 {
		if ($this->client === null) {
			throw new ClientException('platform not loaded');
		}

		return $this->client;
	}
}
