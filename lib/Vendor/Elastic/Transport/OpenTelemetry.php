<?php

/**
 * Elastic Transport
 *
 * @link      https://github.com/elastic/elastic-transport-php
 * @copyright Copyright (c) Elasticsearch B.V (https://www.elastic.co)
 * @license   https://opensource.org/licenses/MIT MIT License
 *
 * Licensed to Elasticsearch B.V under one or more agreements.
 * Elasticsearch B.V licenses this file to you under the MIT License.
 * See the LICENSE file in the project root for more information.
 */
declare (strict_types=1);
namespace OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport;

use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\Exception\InvalidArgumentException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Transport\Serializer\JsonSerializer;
use OCA\FullTextSearch_Elasticsearch\Vendor\OpenTelemetry\API\Trace\TracerInterface;
use OCA\FullTextSearch_Elasticsearch\Vendor\OpenTelemetry\API\Trace\TracerProviderInterface;
class OpenTelemetry
{
    const OTEL_TRACER_NAME = 'elasticsearch-api';
    const PSR7_OTEL_ATTRIBUTE_NAME = 'otel-elastic-transport';
    // Valid values for the enabled config are 'true' and 'false'
    const ENV_VARIABLE_ENABLED = 'OTEL_PHP_INSTRUMENTATION_ELASTICSEARCH_ENABLED';
    /**
     * Describes how to handle search queries in the request body when assigned to
     * span attribute.
     * Valid values are 'raw', 'omit', 'sanitize'. Default is 'omit'
     */
    const ALLOWED_BODY_STRATEGIES = ['raw', 'omit', 'sanitize'];
    const ENV_VARIABLE_BODY_STRATEGY = 'OTEL_PHP_INSTRUMENTATION_ELASTICSEARCH_CAPTURE_SEARCH_QUERY';
    const DEFAULT_BODY_STRATEGY = 'omit';
    /**
     * A string list of keys whose values are redacted. This is only relevant if the body strategy is
     * 'sanitize'. For example, a config 'sensitive-key,other-key' will redact the values at
     * 'sensitive-key' and 'other-key' in addition to the default keys
     */
    const ENV_VARIABLE_BODY_SANITIZE_KEYS = 'OTEL_PHP_INSTRUMENTATION_ELASTICSEARCH_SEARCH_QUERY_SANITIZE_KEYS';
    const DEFAULT_SANITIZER_KEY_PATTERNS = ['password', 'passwd', 'pwd', 'secret', 'key', 'token', 'session', 'credit', 'card', 'auth', 'set-cookie', 'email', 'tel', 'phone'];
    const REDACTED_STRING = 'REDACTED';
    public static function redactBody(string $body) : string
    {
        switch (self::getBodyStrategy()) {
            case 'sanitize':
                $sanitizeKeys = \getenv(self::ENV_VARIABLE_BODY_SANITIZE_KEYS);
                $sanitizeKeys = \false !== $sanitizeKeys ? \explode(',', $sanitizeKeys) : [];
                return self::sanitizeBody($body, $sanitizeKeys);
            case 'raw':
                return $body;
            default:
                return '';
        }
    }
    private static function getBodyStrategy() : string
    {
        $strategy = \getenv(self::ENV_VARIABLE_BODY_STRATEGY);
        if (\false === $strategy) {
            $strategy = self::DEFAULT_BODY_STRATEGY;
        }
        if (!\in_array($strategy, self::ALLOWED_BODY_STRATEGIES)) {
            throw new InvalidArgumentException(\sprintf('The body strategy specified %s is not valid. The available strategies are %s', $strategy, \implode(',', self::ALLOWED_BODY_STRATEGIES)));
        }
        return $strategy;
    }
    public static function getTracer(TracerProviderInterface $tracerProvider) : TracerInterface
    {
        return $tracerProvider->getTracer(self::OTEL_TRACER_NAME, Transport::VERSION);
    }
    private static function sanitizeBody(string $body, array $sanitizeKeys) : string
    {
        if (empty($body)) {
            return '';
        }
        $json = \json_decode($body, \true);
        if (!\is_array($json)) {
            return '';
        }
        $patterns = \array_merge(self::DEFAULT_SANITIZER_KEY_PATTERNS, $sanitizeKeys);
        // Convert the patterns array into a regex
        $regex = \sprintf('/%s/', \implode('|', $patterns));
        // Recursively traverse the array and redact the specified keys
        \array_walk_recursive($json, function (&$value, $key) use($regex) {
            if (\preg_match($regex, $key, $matches)) {
                $value = self::REDACTED_STRING;
            }
        });
        return JsonSerializer::serialize($json);
    }
}
