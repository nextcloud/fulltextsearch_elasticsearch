<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp;

use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\MessageInterface;

interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string;
}
