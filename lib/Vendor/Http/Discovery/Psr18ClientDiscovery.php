<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery;

use OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Exception\DiscoveryFailedException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Exception\NotFoundException as RealNotFoundException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Client\ClientInterface;
/**
 * Finds a PSR-18 HTTP Client.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class Psr18ClientDiscovery extends ClassDiscovery
{
    /**
     * Finds a PSR-18 HTTP Client.
     *
     * @return ClientInterface
     *
     * @throws RealNotFoundException
     */
    public static function find()
    {
        try {
            $client = static::findOneByType(ClientInterface::class);
        } catch (DiscoveryFailedException $e) {
            throw new RealNotFoundException('No PSR-18 clients found. Make sure to install a package providing "psr/http-client-implementation". Example: "php-http/guzzle7-adapter".', 0, $e);
        }
        return static::instantiateClass($client);
    }
}
