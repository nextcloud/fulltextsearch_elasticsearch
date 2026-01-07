<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Client;

use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\RequestInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\ResponseInterface;
interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
