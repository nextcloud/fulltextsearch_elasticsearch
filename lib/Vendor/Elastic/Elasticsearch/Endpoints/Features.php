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
namespace OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints;

use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\ClientResponseException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\MissingParameterException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\ServerResponseException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Response\Elasticsearch;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\Exception\NoNodeAvailableException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Http\Promise\Promise;
/**
 * @generated This file is generated, please do not edit
 * @internal
 */
class Features extends AbstractEndpoint
{
    /**
     * Get the features
     *
     * @link https://www.elastic.co/docs/api/doc/elasticsearch/operation/operation-features-get-features
     *
     * @param array{
     *     master_timeout?: int|string, // Explicit operation timeout for connection to master node
     *     pretty?: bool, // Pretty format the returned JSON response. (DEFAULT: false)
     *     human?: bool, // Return human readable values for statistics. (DEFAULT: true)
     *     error_trace?: bool, // Include the stack trace of returned errors. (DEFAULT: false)
     *     source?: string, // The URL-encoded request definition. Useful for libraries that do not accept a request body for non-POST requests.
     *     filter_path?: string|array<string>, // A comma-separated list of filters used to reduce the response.
     * } $params
     *
     * @throws NoNodeAvailableException if all the hosts are offline
     * @throws ClientResponseException if the status code of response is 4xx
     * @throws ServerResponseException if the status code of response is 5xx
     *
     * @return Elasticsearch|Promise
     */
    public function getFeatures(?array $params = null)
    {
        $params = $params ?? [];
        $url = '/_features';
        $method = 'GET';
        $url = $this->addQueryString($url, $params, ['master_timeout', 'pretty', 'human', 'error_trace', 'source', 'filter_path']);
        $headers = ['Accept' => 'application/json'];
        $request = $this->createRequest($method, $url, $headers, $params['body'] ?? null);
        $request = $this->addOtelAttributes($params, [], $request, 'features.get_features');
        return $this->client->sendRequest($request);
    }
    /**
     * Reset the features
     *
     * @link https://www.elastic.co/docs/api/doc/elasticsearch/operation/operation-features-reset-features
     * @internal This API is EXPERIMENTAL and may be changed or removed completely in a future release
     *
     * @param array{
     *     master_timeout?: int|string, // Explicit operation timeout for connection to master node
     *     pretty?: bool, // Pretty format the returned JSON response. (DEFAULT: false)
     *     human?: bool, // Return human readable values for statistics. (DEFAULT: true)
     *     error_trace?: bool, // Include the stack trace of returned errors. (DEFAULT: false)
     *     source?: string, // The URL-encoded request definition. Useful for libraries that do not accept a request body for non-POST requests.
     *     filter_path?: string|array<string>, // A comma-separated list of filters used to reduce the response.
     * } $params
     *
     * @throws NoNodeAvailableException if all the hosts are offline
     * @throws ClientResponseException if the status code of response is 4xx
     * @throws ServerResponseException if the status code of response is 5xx
     *
     * @return Elasticsearch|Promise
     */
    public function resetFeatures(?array $params = null)
    {
        $params = $params ?? [];
        $url = '/_features/_reset';
        $method = 'POST';
        $url = $this->addQueryString($url, $params, ['master_timeout', 'pretty', 'human', 'error_trace', 'source', 'filter_path']);
        $headers = ['Accept' => 'application/json'];
        $request = $this->createRequest($method, $url, $headers, $params['body'] ?? null);
        $request = $this->addOtelAttributes($params, [], $request, 'features.reset_features');
        return $this->client->sendRequest($request);
    }
}
