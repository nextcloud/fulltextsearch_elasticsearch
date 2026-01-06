<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Client\Exception;

use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\RequestInterface;
trait RequestAwareTrait
{
    /**
     * @var RequestInterface
     */
    private $request;
    private function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
