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
namespace OCA\FullTextSearch_Elasticsearch\Vendor8\Elastic\Elasticsearch\Transport;

use OCA\FullTextSearch_Elasticsearch\Vendor8\Elastic\Elasticsearch\Response\Elasticsearch;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Elastic\Transport\Async\OnSuccessInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\ResponseInterface;
class AsyncOnSuccessNoException implements OnSuccessInterface
{
    public function success(ResponseInterface $response, int $count): Elasticsearch
    {
        $result = new Elasticsearch();
        $result->setResponse($response, \false);
        return $result;
    }
}
