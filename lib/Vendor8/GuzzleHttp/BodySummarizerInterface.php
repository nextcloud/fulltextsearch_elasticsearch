<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor8\GuzzleHttp;

use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string;
}
