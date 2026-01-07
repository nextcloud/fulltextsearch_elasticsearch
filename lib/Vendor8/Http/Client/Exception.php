<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Client;

use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Client\ClientExceptionInterface as PsrClientException;
/**
 * Every HTTP Client related Exception must implement this interface.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
interface Exception extends PsrClientException
{
}
