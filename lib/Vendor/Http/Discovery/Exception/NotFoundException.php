<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Exception;

use OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Exception;
/**
 * Thrown when a discovery does not find any matches.
 *
 * @final do NOT extend this class, not final for BC reasons
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 * @internal
 */
/* final */
class NotFoundException extends \RuntimeException implements Exception
{
}
