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
 */
class Profiling extends AbstractEndpoint
{
    /**
     * Extracts a UI-optimized structure to render flamegraphs from Universal Profiling.
     *
     * @see https://www.elastic.co/guide/en/observability/current/universal-profiling.html
     *
     * @param array{
     *     pretty: boolean, // Pretty format the returned JSON response. (DEFAULT: false)
     *     human: boolean, // Return human readable values for statistics. (DEFAULT: true)
     *     error_trace: boolean, // Include the stack trace of returned errors. (DEFAULT: false)
     *     source: string, // The URL-encoded request definition. Useful for libraries that do not accept a request body for non-POST requests.
     *     filter_path: list, // A comma-separated list of filters used to reduce the response.
     *     body: array, // (REQUIRED) The filter conditions for the flamegraph
     * } $params
     *
     * @throws NoNodeAvailableException if all the hosts are offline
     * @throws ClientResponseException if the status code of response is 4xx
     * @throws ServerResponseException if the status code of response is 5xx
     *
     * @return Elasticsearch|Promise
     */
    public function flamegraph(array $params = [])
    {
        $this->checkRequiredParameters(['body'], $params);
        $url = '/_profiling/flamegraph';
        $method = 'POST';
        $url = $this->addQueryString($url, $params, ['pretty', 'human', 'error_trace', 'source', 'filter_path']);
        $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
        return $this->client->sendRequest($this->createRequest($method, $url, $headers, $params['body'] ?? null));
    }
    /**
     * Extracts raw stacktrace information from Universal Profiling.
     *
     * @see https://www.elastic.co/guide/en/observability/current/universal-profiling.html
     *
     * @param array{
     *     pretty: boolean, // Pretty format the returned JSON response. (DEFAULT: false)
     *     human: boolean, // Return human readable values for statistics. (DEFAULT: true)
     *     error_trace: boolean, // Include the stack trace of returned errors. (DEFAULT: false)
     *     source: string, // The URL-encoded request definition. Useful for libraries that do not accept a request body for non-POST requests.
     *     filter_path: list, // A comma-separated list of filters used to reduce the response.
     *     body: array, // (REQUIRED) The filter conditions for stacktraces
     * } $params
     *
     * @throws NoNodeAvailableException if all the hosts are offline
     * @throws ClientResponseException if the status code of response is 4xx
     * @throws ServerResponseException if the status code of response is 5xx
     *
     * @return Elasticsearch|Promise
     */
    public function stacktraces(array $params = [])
    {
        $this->checkRequiredParameters(['body'], $params);
        $url = '/_profiling/stacktraces';
        $method = 'POST';
        $url = $this->addQueryString($url, $params, ['pretty', 'human', 'error_trace', 'source', 'filter_path']);
        $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
        return $this->client->sendRequest($this->createRequest($method, $url, $headers, $params['body'] ?? null));
    }
    /**
     * Returns basic information about the status of Universal Profiling.
     *
     * @see https://www.elastic.co/guide/en/observability/current/universal-profiling.html
     *
     * @param array{
     *     master_timeout: time, // Explicit operation timeout for connection to master node
     *     timeout: time, // Explicit operation timeout
     *     wait_for_resources_created: boolean, // Whether to return immediately or wait until resources have been created
     *     pretty: boolean, // Pretty format the returned JSON response. (DEFAULT: false)
     *     human: boolean, // Return human readable values for statistics. (DEFAULT: true)
     *     error_trace: boolean, // Include the stack trace of returned errors. (DEFAULT: false)
     *     source: string, // The URL-encoded request definition. Useful for libraries that do not accept a request body for non-POST requests.
     *     filter_path: list, // A comma-separated list of filters used to reduce the response.
     * } $params
     *
     * @throws NoNodeAvailableException if all the hosts are offline
     * @throws ClientResponseException if the status code of response is 4xx
     * @throws ServerResponseException if the status code of response is 5xx
     *
     * @return Elasticsearch|Promise
     */
    public function status(array $params = [])
    {
        $url = '/_profiling/status';
        $method = 'GET';
        $url = $this->addQueryString($url, $params, ['master_timeout', 'timeout', 'wait_for_resources_created', 'pretty', 'human', 'error_trace', 'source', 'filter_path']);
        $headers = ['Accept' => 'application/json'];
        return $this->client->sendRequest($this->createRequest($method, $url, $headers, $params['body'] ?? null));
    }
}
