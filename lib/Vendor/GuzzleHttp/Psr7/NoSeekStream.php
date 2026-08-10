<?php

declare (strict_types=1);
namespace OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7;

use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\StreamInterface;
/**
 * Stream decorator that prevents a stream from being seeked.
 */
final class NoSeekStream implements StreamInterface
{
    use StreamDecoratorTrait;
    /** @var StreamInterface */
    private $stream;
    public function seek($offset, $whence = \SEEK_SET): void
    {
        if (!\is_int($offset)) {
            \OCA\FullTextSearch_Elasticsearch\Vendor\trigger_deprecation('guzzlehttp/psr7', '2.11', 'Passing %s to StreamInterface::seek() is deprecated; guzzlehttp/psr7 3.0 requires int for $offset.', \get_debug_type($offset));
        }
        if (!\is_int($whence)) {
            \OCA\FullTextSearch_Elasticsearch\Vendor\trigger_deprecation('guzzlehttp/psr7', '2.11', 'Passing %s to StreamInterface::seek() is deprecated; guzzlehttp/psr7 3.0 requires int for $whence.', \get_debug_type($whence));
        }
        throw new \RuntimeException('Cannot seek a NoSeekStream');
    }
    public function isSeekable(): bool
    {
        return \false;
    }
}
