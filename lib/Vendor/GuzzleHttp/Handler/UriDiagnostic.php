<?php

declare (strict_types=1);
namespace OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Handler;

use OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\UriInterface;
/**
 * @internal
 */
final class UriDiagnostic
{
    private function __construct()
    {
    }
    public static function redactInMessage(
        #[\SensitiveParameter]
        string $message,
        #[\SensitiveParameter]
        UriInterface $uri
    ): string
    {
        $message = Psr7\Utils::redactUserInfoInString($message, (string) $uri);
        $query = $uri->getQuery();
        if ($query !== '') {
            $message = \str_replace('?' . $query, '', $message);
        }
        $fragment = $uri->getFragment();
        if ($fragment !== '') {
            $message = \str_replace('#' . $fragment, '', $message);
        }
        return Psr7\DiagnosticValue::escape($message);
    }
}
