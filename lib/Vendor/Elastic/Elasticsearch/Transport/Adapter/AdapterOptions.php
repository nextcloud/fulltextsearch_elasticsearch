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

/**
 * The HTTP client adapters supported
 */
final class AdapterOptions
{
    const HTTP_ADAPTERS = ["OCA\\FullTextSearch_Elasticsearch\\Vendor\\GuzzleHttp\\Client" => "OCA\\FullTextSearch_Elasticsearch\\Vendor\\Elastic\\Elasticsearch\\Transport\\Adapter\\Guzzle", "OCA\\FullTextSearch_Elasticsearch\\Vendor\\Symfony\\Component\\HttpClient\\HttplugClient" => "OCA\\FullTextSearch_Elasticsearch\\Vendor\\Elastic\\Elasticsearch\\Transport\\Adapter\\Symfony", "OCA\\FullTextSearch_Elasticsearch\\Vendor\\Symfony\\Component\\HttpClient\\Psr18Client" => "OCA\\FullTextSearch_Elasticsearch\\Vendor\\Elastic\\Elasticsearch\\Transport\\Adapter\\Symfony", "OCA\\FullTextSearch_Elasticsearch\\Vendor\\Elastic\\Transport\\Client\\Curl" => "OCA\\FullTextSearch_Elasticsearch\\Vendor\\Elastic\\Elasticsearch\\Transport\\Adapter\\ElasticCurl"];
}
