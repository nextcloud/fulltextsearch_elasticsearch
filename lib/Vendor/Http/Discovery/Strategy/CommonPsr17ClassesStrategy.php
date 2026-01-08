<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor\Http\Discovery\Strategy;

use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\RequestFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\ResponseFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\ServerRequestFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\StreamFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\UploadedFileFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\Psr\Http\Message\UriFactoryInterface;
/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * Don't miss updating src/Composer/Plugin.php when adding a new supported class.
 */
final class CommonPsr17ClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [RequestFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor\Phalcon\Http\Message\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Diactoros\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Guzzle\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Slim\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Laminas\Diactoros\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Slim\Psr7\Factory\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\HttpSoft\Message\RequestFactory'], ResponseFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor\Phalcon\Http\Message\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Diactoros\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Guzzle\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Slim\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Laminas\Diactoros\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Slim\Psr7\Factory\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\HttpSoft\Message\ResponseFactory'], ServerRequestFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor\Phalcon\Http\Message\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Diactoros\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Guzzle\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Slim\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Laminas\Diactoros\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Slim\Psr7\Factory\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\HttpSoft\Message\ServerRequestFactory'], StreamFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor\Phalcon\Http\Message\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Diactoros\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Guzzle\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Slim\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Laminas\Diactoros\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Slim\Psr7\Factory\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\HttpSoft\Message\StreamFactory'], UploadedFileFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor\Phalcon\Http\Message\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Diactoros\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Guzzle\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Slim\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Laminas\Diactoros\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Slim\Psr7\Factory\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\HttpSoft\Message\UploadedFileFactory'], UriFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor\Phalcon\Http\Message\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Diactoros\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Guzzle\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Http\Factory\Slim\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Laminas\Diactoros\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\Slim\Psr7\Factory\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor\HttpSoft\Message\UriFactory']];
    public static function getCandidates($type)
    {
        $candidates = [];
        if (isset(self::$classes[$type])) {
            foreach (self::$classes[$type] as $class) {
                $candidates[] = ['class' => $class, 'condition' => [$class]];
            }
        }
        return $candidates;
    }
}
