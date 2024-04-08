<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Strategy;

use OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Exception\StrategyUnavailableException;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface DiscoveryStrategy
{
    /**
     * Find a resource of a specific type.
     *
     * @param string $type
     *
     * @return array The return value is always an array with zero or more elements. Each
     *               element is an array with two keys ['class' => string, 'condition' => mixed].
     *
     * @throws StrategyUnavailableException if we cannot use this strategy
     */
    public static function getCandidates($type);
}
