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
namespace OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Transport\Adapter;

use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Client\ClientInterface;
interface AdapterInterface
{
    public function setConfig(ClientInterface $client, array $config, array $clientOptions) : ClientInterface;
}