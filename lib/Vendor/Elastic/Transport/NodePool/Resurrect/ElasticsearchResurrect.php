<?php

/**
 * Elastic Transport
 *
 * @link      https://github.com/elastic/elastic-transport-php
 * @copyright Copyright (c) Elasticsearch B.V (https://www.elastic.co)
 * @license   https://opensource.org/licenses/MIT MIT License
 *
 * Licensed to Elasticsearch B.V under one or more agreements.
 * Elasticsearch B.V licenses this file to you under the MIT License.
 * See the LICENSE file in the project root for more information.
 */
declare (strict_types=1);
namespace OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\NodePool\Resurrect;

use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\NodePool\Node;
use Exception;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Client\ClientInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Psr17FactoryDiscovery;
use OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Psr18ClientDiscovery;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\RequestFactoryInterface;
class ElasticsearchResurrect implements ResurrectInterface
{
    protected ClientInterface $client;
    protected RequestFactoryInterface $requestFactory;
    public function ping(Node $node) : bool
    {
        $request = $this->getRequestFactory()->createRequest("HEAD", $node->getUri());
        try {
            $response = $this->getClient()->sendRequest($request);
            return $response->getStatusCode() === 200;
        } catch (Exception $e) {
            return \false;
        }
    }
    public function getClient() : ClientInterface
    {
        if (empty($this->client)) {
            $this->client = Psr18ClientDiscovery::find();
        }
        return $this->client;
    }
    public function getRequestFactory() : RequestFactoryInterface
    {
        if (empty($this->requestFactory)) {
            $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        }
        return $this->requestFactory;
    }
}
