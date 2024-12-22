<?php

namespace OCA\FullTextSearch_OpenSearch\Service;

use OCA\FullTextSearch_OpenSearch\Service\ConfigService;
use OCP\IConfig;
use PHPUnit\Framework\TestCase;



class ConfigServiceTest extends TestCase
{
    private IConfig $configMock;
    private ConfigService $configService;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(IConfig::class);
        $this->configService = new ConfigService($this->configMock);
    }

    public function testCheckConfigValidData(): void
    {
        $validData = [
            ConfigService::OPENSEARCH_HOST => 'https://example.com',
            ConfigService::OPENSEARCH_INDEX => 'valid_index',
            ConfigService::ANALYZER_TOKENIZER => 'standard',
        ];

        $result = $this->configService->checkConfig($validData);

        $this->assertEmpty($result);
    }

    public function testCheckConfigMissingRequiredKeys(): void
    {
        $invalidData = [
            ConfigService::OPENSEARCH_INDEX => 'valid_index',
        ];

        $result = $this->configService->checkConfig($invalidData);

        $this->assertEquals([ConfigService::OPENSEARCH_HOST], $result);
    }

    public function testCheckConfigInvalidHostUrl(): void
    {
        $invalidData = [
            ConfigService::OPENSEARCH_HOST => 'invalid-url',
            ConfigService::OPENSEARCH_INDEX => 'valid_index',
            ConfigService::ANALYZER_TOKENIZER => 'standard',
        ];

        $result = $this->configService->checkConfig($invalidData);

        $this->assertEquals([ConfigService::OPENSEARCH_HOST], $result);
    }

    public function testCheckConfigInvalidHostScheme(): void
    {
        $invalidData = [
            ConfigService::OPENSEARCH_HOST => 'ftp://example.com',
            ConfigService::OPENSEARCH_INDEX => 'valid_index',
            ConfigService::ANALYZER_TOKENIZER => 'standard',
        ];

        $result = $this->configService->checkConfig($invalidData);

        $this->assertEquals([ConfigService::OPENSEARCH_HOST], $result);
    }

    public function testCheckConfigInvalidIndexName(): void
    {
        $invalidData = [
            ConfigService::OPENSEARCH_HOST => 'https://example.com',
            ConfigService::OPENSEARCH_INDEX => '-invalid_index',
            ConfigService::ANALYZER_TOKENIZER => 'standard',
        ];

        $result = $this->configService->checkConfig($invalidData);

        $this->assertEquals([ConfigService::OPENSEARCH_INDEX], $result);
    }
}