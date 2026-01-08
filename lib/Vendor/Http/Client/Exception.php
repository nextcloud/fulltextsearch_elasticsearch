<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor\Http\Client;

use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Client\ClientExceptionInterface as PsrClientException;
/**
 * Every HTTP Client related Exception must implement this interface.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 * @internal
 */
interface Exception extends PsrClientException
{
}
