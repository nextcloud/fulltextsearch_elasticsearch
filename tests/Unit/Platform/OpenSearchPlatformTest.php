<?php

namespace OCA\FullTextSearch_OpenSearch\Platform;

use OCA\FullTextSearch_OpenSearch\Service\ConfigService;
use OCA\FullTextSearch_OpenSearch\Service\IndexService;
use OCA\FullTextSearch_OpenSearch\Service\SearchService;
use OCA\FullTextSearch_OpenSearch\Platform\Client;
use OCP\FullTextSearch\Model\IRunner;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Unit tests for the OpenSearchPlatform class.
 *
 * Specifically, this test class ensures that the getName() and other methods
 * return the expected values.
 */
class OpenSearchPlatformTest extends TestCase
{
    private OpenSearchPlatform $openSearchPlatform;
    private $configServiceMock;

    protected function setUp(): void
    {
        $this->configServiceMock = $this->createMock(ConfigService::class);
        $this->indexServiceMock = $this->createMock(IndexService::class);
        $searchServiceMock = $this->createMock(SearchService::class);
        $loggerMock = $this->createMock(LoggerInterface::class);
        $runnerMock = $this->createMock(IRunner::class);

        $this->runnerMock = $runnerMock;

        $this->openSearchPlatform = new OpenSearchPlatform(
            $this->configServiceMock,
            $this->indexServiceMock,
            $searchServiceMock,
            $loggerMock
        );
    }

    public function testGetIdReturnsCorrectValue(): void
    {
        $this->assertSame('open_search', $this->openSearchPlatform->getId());
    }

    public function testGetNameReturnsCorrectValue(): void
    {
        $this->assertSame('OpenSearch', $this->openSearchPlatform->getName());
    }

    public function testGetConfigurationReturnsCorrectValue(): void
    {
        $configMockData = ['index_name' => 'test_index', 'logger_enabled' => true];
        $hostsMockData = [
            'https://username:password@testhost1.com:9201',
            'https://username:password@testhost2.com:9202',
        ];
        $expectedSanitizedHosts = [
            'https://username:********@testhost1.com:9201',
            'https://username:********@testhost2.com:9202',
        ];
        $expectedConfig = array_merge($configMockData, ['opensearch_host' => $expectedSanitizedHosts]);

        $this->configServiceMock->method('getConfig')->willReturn($configMockData);
        $this->configServiceMock->method('getOpenSearchHost')->willReturn($hostsMockData);

        $actualConfig = $this->openSearchPlatform->getConfiguration();

        $this->assertSame($expectedConfig, $actualConfig);
    }
    public function testSetRunnerAssignsRunnerCorrectly(): void
    {
        $this->openSearchPlatform->setRunner($this->runnerMock);

        $reflection = new \ReflectionClass($this->openSearchPlatform);
        $property = $reflection->getProperty('runner');
        $property->setAccessible(true);
        $actualRunner = $property->getValue($this->openSearchPlatform);

        $this->assertSame($this->runnerMock, $actualRunner);
    }
}