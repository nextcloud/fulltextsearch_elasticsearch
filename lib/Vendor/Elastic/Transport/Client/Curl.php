<?php

/**
 * Elastic Transport
 *
 * @link      https://github.com/elastic/elastic-transport-php
 * @copyright Copyright (c) Elasticsearch B.V (https://www.elastic.co)
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 *
 * Licensed to Elasticsearch B.V under one or more agreements.
 * Elasticsearch B.V licenses this file to you under the Apache 2.0 License.
 * See the LICENSE file in the project root for more information.
 */
declare (strict_types=1);
namespace OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\Client;

use CurlHandle;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\Exception\CurlException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Nyholm\Psr7\Response;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Client\ClientInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\RequestInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\ResponseInterface;
use function curl_close;
use function curl_error;
use function curl_errno;
use function curl_exec;
use function curl_init;
use function curl_reset;
use function curl_setopt_array;

class Curl implements ClientInterface
{
    const DEFAULT_CONNECTION_TIMEOUT = 0;
    private const BODYLESS_HTTP_METHODS = ['HEAD', 'GET'];
    private const HTTP_SPEC_CRLF = "\r\n";
    private const HTTP_SPEC_SP = " ";
    private const STALE_CONNECTION_ERROR_CODES = [52, 56];
    private const STALE_CONNECTION_RETRIES = 1;
    /**
     * @var CurlHandle $curl
     */
    protected ?CurlHandle $curl = null;
    /**
     * @var array<mixed>
     */
    protected array $options;
    /**
     * @param array<mixed> $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }
    /**
     * @param array<mixed> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
    /**
     * @throws CurlException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $headers = [];
        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = sprintf("%s: %s", $name, $value);
            }
        }
        $curlOptions = [\CURLOPT_HTTP_VERSION => $this->getCurlHttpVersion($request->getProtocolVersion()), \CURLOPT_CUSTOMREQUEST => $request->getMethod(), \CURLOPT_CONNECTTIMEOUT => 0, \CURLOPT_URL => (string) $request->getUri(), \CURLOPT_NOBODY => $request->getMethod() === 'HEAD', \CURLOPT_RETURNTRANSFER => \true, \CURLOPT_HEADER => \true, \CURLOPT_HTTPHEADER => $headers];
        if (!in_array($request->getMethod(), self::BODYLESS_HTTP_METHODS, \true)) {
            $curlOptions[\CURLOPT_POSTFIELDS] = (string) $request->getBody();
        }

        return $this->sendRequestWithRetry($curlOptions);
    }

    /**
     * @param array<mixed> $curlOptions
     *
     * @throws CurlException
     */
    private function sendRequestWithRetry(array $curlOptions): ResponseInterface
    {
        $attempt = 0;
        while (\true) {
            $curl = $this->prepareCurl($attempt > 0);
            curl_setopt_array(
                $curl,
                array_replace($curlOptions, $this->options, $this->getRetryOptions($attempt > 0))
            );

            $response = curl_exec($curl);
            $errno = curl_errno($curl);
            if ($response !== \false && $errno === 0) {
                $parse = $this->parseResponse((string) $response);

                return new Response(
                    $parse['status-code'],
                    $parse['headers'],
                    $parse['body'],
                    $parse['http-version'],
                    $parse['reason-phrase']
                );
            }

            $error = curl_error($curl);
            if ($attempt < self::STALE_CONNECTION_RETRIES && $this->shouldRetryWithFreshConnection($errno)) {
                $attempt++;
                continue;
            }

            throw new CurlException(sprintf("Error sending with cURL (%d): %s", $errno, $error));
        }
    }

    private function getCurl(): CurlHandle
    {
        if ($this->curl === null) {
            $init = curl_init();
            if (\false === $init) {
                throw new CurlException("I cannot execute curl initialization");
            }
            $this->curl = $init;
        }
        return $this->curl;
    }

    private function prepareCurl(bool $freshConnection): CurlHandle
    {
        if ($freshConnection) {
            $this->resetCurl();
        }

        $curl = $this->getCurl();
        curl_reset($curl);

        return $curl;
    }

    /**
     * @return array<mixed>
     */
    private function getRetryOptions(bool $freshConnection): array
    {
        if (!$freshConnection) {
            return [];
        }

        $options = [
            \CURLOPT_FORBID_REUSE => \true,
            \CURLOPT_FRESH_CONNECT => \true,
        ];

        if (defined('CURLOPT_TCP_KEEPALIVE')) {
            $options[\CURLOPT_TCP_KEEPALIVE] = 0;
        }

        return $options;
    }

    private function shouldRetryWithFreshConnection(int $errno): bool
    {
        return in_array($errno, self::STALE_CONNECTION_ERROR_CODES, \true);
    }

    private function resetCurl(): void
    {
        if ($this->curl === null) {
            return;
        }

        curl_close($this->curl);
        $this->curl = null;
    }
    /**
     * Return cURL constant for specified HTTP version.
     *
     * @throws CurlException If unsupported version requested.
     */
    private function getCurlHttpVersion(string $version): int
    {
        switch ($version) {
            case '1.0':
                return \CURL_HTTP_VERSION_1_0;
            case '1.1':
                return \CURL_HTTP_VERSION_1_1;
            case '2.0':
                if (defined('CURL_HTTP_VERSION_2_0')) {
                    return \CURL_HTTP_VERSION_2_0;
                }
                throw new CurlException('libcurl 7.33 needed for HTTP 2.0 support');
        }
        return \CURL_HTTP_VERSION_NONE;
    }
    /**
     * Parses the HTTP response from curl and
     * generates the start-line, headers and the body
     *
     * @see https://datatracker.ietf.org/doc/html/rfc7230#section-3
     *
     * @return array{
     *      http-version: string, // HTTP version (e.g. if HTTP/1.1 http-version is "1.1")
     *      status-code: int, // The status code of the response (e.g. 200)
     *      reason-phrase: string, // The reason-phrase (e.g. OK)
     *      headers: array<mixed>, // The HTTP headers
     *      body: string, // The body content (can be empty)
     * }
     */
    private function parseResponse(string $response): array
    {
        $lines = explode(self::HTTP_SPEC_CRLF, $response);
        $output = ['http-version' => '', 'status-code' => 200, 'reason-phrase' => '', 'headers' => [], 'body' => ''];
        foreach ($lines as $i => $line) {
            // status-line
            // @see https://datatracker.ietf.org/doc/html/rfc7230#section-3.1.2
            if ($i === 0) {
                $statusLine = explode(self::HTTP_SPEC_SP, $line, 3);
                $output['http-version'] = explode('/', $statusLine[0], 2)[1];
                $output['status-code'] = (int) $statusLine[1];
                $output['reason-phrase'] = $statusLine[2];
                continue;
            }
            // Empty line, end of headers
            if (empty($line)) {
                $output['body'] = $lines[$i + 1] ?? '';
                break;
            }
            // Extract header name and values
            [$name, $value] = explode(':', $line, 2);
            if (!isset($output['headers'][$name])) {
                $output['headers'][$name] = [$value];
            } else {
                $output['headers'][$name][] = $value;
            }
        }
        return $output;
    }
}
