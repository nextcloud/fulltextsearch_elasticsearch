<?php

namespace OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Discovery\Strategy;

use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\RequestFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\ResponseFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\ServerRequestFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\StreamFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\UploadedFileFactoryInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor8\Psr\Http\Message\UriFactoryInterface;
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
    private static $classes = [RequestFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor8\Phalcon\Http\Message\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Diactoros\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Guzzle\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Slim\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Laminas\Diactoros\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Slim\Psr7\Factory\RequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\HttpSoft\Message\RequestFactory'], ResponseFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor8\Phalcon\Http\Message\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Diactoros\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Guzzle\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Slim\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Laminas\Diactoros\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Slim\Psr7\Factory\ResponseFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\HttpSoft\Message\ResponseFactory'], ServerRequestFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor8\Phalcon\Http\Message\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Diactoros\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Guzzle\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Slim\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Laminas\Diactoros\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Slim\Psr7\Factory\ServerRequestFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\HttpSoft\Message\ServerRequestFactory'], StreamFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor8\Phalcon\Http\Message\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Diactoros\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Guzzle\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Slim\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Laminas\Diactoros\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Slim\Psr7\Factory\StreamFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\HttpSoft\Message\StreamFactory'], UploadedFileFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor8\Phalcon\Http\Message\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Diactoros\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Guzzle\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Slim\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Laminas\Diactoros\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Slim\Psr7\Factory\UploadedFileFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\HttpSoft\Message\UploadedFileFactory'], UriFactoryInterface::class => ['OCA\FullTextSearch_Elasticsearch\Vendor8\Phalcon\Http\Message\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Nyholm\Psr7\Factory\Psr17Factory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\GuzzleHttp\Psr7\HttpFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Diactoros\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Guzzle\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Http\Factory\Slim\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Laminas\Diactoros\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\Slim\Psr7\Factory\UriFactory', 'OCA\FullTextSearch_Elasticsearch\Vendor8\HttpSoft\Message\UriFactory']];
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
