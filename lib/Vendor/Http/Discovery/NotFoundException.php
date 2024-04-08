<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery;

use OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Exception\NotFoundException as RealNotFoundException;

/**
 * Thrown when a discovery does not find any matches.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @deprecated since since version 1.0, and will be removed in 2.0. Use {@link \OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Exception\NotFoundException} instead.
 */
final class NotFoundException extends RealNotFoundException
{
}
