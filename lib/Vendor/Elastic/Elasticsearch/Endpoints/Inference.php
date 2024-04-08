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

declare(strict_types=1);

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
class Inference extends AbstractEndpoint
{
	/**
	 * Delete model in the Inference API
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/reference/master/delete-inference-api.html
	 * @internal This API is EXPERIMENTAL and may be changed or removed completely in a future release
	 *
	 * @param array{
	 *     inference_id: string, // (REQUIRED) The model Id
	 *     task_type: string, //  The task type
	 *     pretty: boolean, // Pretty format the returned JSON response. (DEFAULT: false)
	 *     human: boolean, // Return human readable values for statistics. (DEFAULT: true)
	 *     error_trace: boolean, // Include the stack trace of returned errors. (DEFAULT: false)
	 *     source: string, // The URL-encoded request definition. Useful for libraries that do not accept a request body for non-POST requests.
	 *     filter_path: list, // A comma-separated list of filters used to reduce the response.
	 * } $params
	 *
	 * @throws MissingParameterException if a required parameter is missing
	 * @throws NoNodeAvailableException if all the hosts are offline
	 * @throws ClientResponseException if the status code of response is 4xx
	 * @throws ServerResponseException if the status code of response is 5xx
	 *
	 * @return Elasticsearch|Promise
	 */
	public function deleteModel(array $params = [])
	{
		$this->checkRequiredParameters(['inference_id'], $params);
		if (isset($params['task_type'])) {
			$url = '/_inference/' . $this->encode($params['task_type']) . '/' . $this->encode($params['inference_id']);
			$method = 'DELETE';
		} else {
			$url = '/_inference/' . $this->encode($params['inference_id']);
			$method = 'DELETE';
		}
		$url = $this->addQueryString($url, $params, ['pretty','human','error_trace','source','filter_path']);
		$headers = [
			'Accept' => 'application/json',
		];
		return $this->client->sendRequest($this->createRequest($method, $url, $headers, $params['body'] ?? null));
	}


	/**
	 * Get a model in the Inference API
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/reference/master/get-inference-api.html
	 * @internal This API is EXPERIMENTAL and may be changed or removed completely in a future release
	 *
	 * @param array{
	 *     inference_id: string, // (REQUIRED) The inference Id
	 *     task_type: string, //  The task type
	 *     pretty: boolean, // Pretty format the returned JSON response. (DEFAULT: false)
	 *     human: boolean, // Return human readable values for statistics. (DEFAULT: true)
	 *     error_trace: boolean, // Include the stack trace of returned errors. (DEFAULT: false)
	 *     source: string, // The URL-encoded request definition. Useful for libraries that do not accept a request body for non-POST requests.
	 *     filter_path: list, // A comma-separated list of filters used to reduce the response.
	 * } $params
	 *
	 * @throws MissingParameterException if a required parameter is missing
	 * @throws NoNodeAvailableException if all the hosts are offline
	 * @throws ClientResponseException if the status code of response is 4xx
	 * @throws ServerResponseException if the status code of response is 5xx
	 *
	 * @return Elasticsearch|Promise
	 */
	public function getModel(array $params = [])
	{
		$this->checkRequiredParameters(['inference_id'], $params);
		if (isset($params['task_type'])) {
			$url = '/_inference/' . $this->encode($params['task_type']) . '/' . $this->encode($params['inference_id']);
			$method = 'GET';
		} else {
			$url = '/_inference/' . $this->encode($params['inference_id']);
			$method = 'GET';
		}
		$url = $this->addQueryString($url, $params, ['pretty','human','error_trace','source','filter_path']);
		$headers = [
			'Accept' => 'application/json',
		];
		return $this->client->sendRequest($this->createRequest($method, $url, $headers, $params['body'] ?? null));
	}


	/**
	 * Perform inference on a model
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/reference/master/post-inference-api.html
	 * @internal This API is EXPERIMENTAL and may be changed or removed completely in a future release
	 *
	 * @param array{
	 *     inference_id: string, // (REQUIRED) The inference Id
	 *     task_type: string, //  The task type
	 *     pretty: boolean, // Pretty format the returned JSON response. (DEFAULT: false)
	 *     human: boolean, // Return human readable values for statistics. (DEFAULT: true)
	 *     error_trace: boolean, // Include the stack trace of returned errors. (DEFAULT: false)
	 *     source: string, // The URL-encoded request definition. Useful for libraries that do not accept a request body for non-POST requests.
	 *     filter_path: list, // A comma-separated list of filters used to reduce the response.
	 *     body: array, //  The inference payload
	 * } $params
	 *
	 * @throws MissingParameterException if a required parameter is missing
	 * @throws NoNodeAvailableException if all the hosts are offline
	 * @throws ClientResponseException if the status code of response is 4xx
	 * @throws ServerResponseException if the status code of response is 5xx
	 *
	 * @return Elasticsearch|Promise
	 */
	public function inference(array $params = [])
	{
		$this->checkRequiredParameters(['inference_id'], $params);
		if (isset($params['task_type'])) {
			$url = '/_inference/' . $this->encode($params['task_type']) . '/' . $this->encode($params['inference_id']);
			$method = 'POST';
		} else {
			$url = '/_inference/' . $this->encode($params['inference_id']);
			$method = 'POST';
		}
		$url = $this->addQueryString($url, $params, ['pretty','human','error_trace','source','filter_path']);
		$headers = [
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
		];
		return $this->client->sendRequest($this->createRequest($method, $url, $headers, $params['body'] ?? null));
	}


	/**
	 * Configure a model for use in the Inference API
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/reference/master/put-inference-api.html
	 * @internal This API is EXPERIMENTAL and may be changed or removed completely in a future release
	 *
	 * @param array{
	 *     inference_id: string, // (REQUIRED) The inference Id
	 *     task_type: string, //  The task type
	 *     pretty: boolean, // Pretty format the returned JSON response. (DEFAULT: false)
	 *     human: boolean, // Return human readable values for statistics. (DEFAULT: true)
	 *     error_trace: boolean, // Include the stack trace of returned errors. (DEFAULT: false)
	 *     source: string, // The URL-encoded request definition. Useful for libraries that do not accept a request body for non-POST requests.
	 *     filter_path: list, // A comma-separated list of filters used to reduce the response.
	 *     body: array, //  The model's task and service settings
	 * } $params
	 *
	 * @throws MissingParameterException if a required parameter is missing
	 * @throws NoNodeAvailableException if all the hosts are offline
	 * @throws ClientResponseException if the status code of response is 4xx
	 * @throws ServerResponseException if the status code of response is 5xx
	 *
	 * @return Elasticsearch|Promise
	 */
	public function putModel(array $params = [])
	{
		$this->checkRequiredParameters(['inference_id'], $params);
		if (isset($params['task_type'])) {
			$url = '/_inference/' . $this->encode($params['task_type']) . '/' . $this->encode($params['inference_id']);
			$method = 'PUT';
		} else {
			$url = '/_inference/' . $this->encode($params['inference_id']);
			$method = 'PUT';
		}
		$url = $this->addQueryString($url, $params, ['pretty','human','error_trace','source','filter_path']);
		$headers = [
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
		];
		return $this->client->sendRequest($this->createRequest($method, $url, $headers, $params['body'] ?? null));
	}
}
