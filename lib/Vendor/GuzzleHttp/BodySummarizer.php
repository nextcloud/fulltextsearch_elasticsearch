<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp;

use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\MessageInterface;

final class BodySummarizer implements BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;

    public function __construct(int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }

    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string
    {
        return $this->truncateAt === null
            ? \OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7\Message::bodySummary($message)
            : \OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
