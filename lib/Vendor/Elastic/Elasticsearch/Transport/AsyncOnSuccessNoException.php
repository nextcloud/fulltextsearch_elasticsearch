<?php

/**
 * Elasticsearch PHP Client
 *
 * @link      https://github.com/elastic/elasticsearch-php
 * @copyright Copyright (c) Elasticsearch B.V (https://www.elastic.co)
 * @license   https://opensource.org/licenses/MIT MIT License
 *
 * Licensed to Elasticsearch B.V under one or more agreements.
 * Elasticsearch B.V licenses this file to you under the MIT License.
 * See the LICENSE file in the project root for more information.
 */
declare (strict_types=1);
namespace OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Transport;

use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\ClientInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Response\Elasticsearch;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\Async\OnSuccessInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\ResponseInterface;
class AsyncOnSuccessNoException implements OnSuccessInterface
{
    public function __construct(protected ?ClientInterface $client = null)
    {
    }
    public function success(ResponseInterface $response, int $count): Elasticsearch
    {
        $result = new Elasticsearch();
        $result->setResponse($response, \false);
        if (isset($this->client)) {
            $this->client->setServerless($result->isServerless());
        }
        return $result;
    }
}
