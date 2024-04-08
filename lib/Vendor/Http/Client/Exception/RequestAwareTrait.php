<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor\Http\Client\Exception;

use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\RequestInterface;

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

    /**
     * {@inheritdoc}
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
