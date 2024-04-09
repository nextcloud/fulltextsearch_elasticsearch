<?php

/*
 * This file is part of the Fidry\Console package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fidry\Console\Command;

use function get_debug_type;
use function is_array;
use function is_bool;
use function is_string;
use function Safe\sprintf;
use Symfony\Component\Console\Exception\InvalidArgumentException as ConsoleInvalidArgumentException;
use function var_export;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException as AssertInvalidArgumentException;

/**
 * @private
 */
final class ConsoleAssert
{
    private function __construct()
    {
    }

    /**
     * @param mixed $argument
     *
     * @psalm-assert null|string|string[] $argument
     */
    public static function assertIsValidArgumentType($argument): void
    {
        if (null === $argument || is_string($argument)) {
            return;
        }

        if (!is_array($argument)) {
            throw new ConsoleInvalidArgumentException(
                sprintf(
                    'Expected an argument value type to be "null|string|string[]". Got "%s"',
                    get_debug_type($argument),
                ),
            );
        }

        foreach ($argument as $item) {
            self::assertIsValidArgumentType($item);
        }
    }

    /**
     * @param mixed $option
     *
     * @psalm-assert null|bool|string|string[] $argument
     */
    public static function assertIsValidOptionType($option): void
    {
        if (null === $option || is_bool($option) || is_string($option)) {
            return;
        }

        if (!is_array($option)) {
            throw new ConsoleInvalidArgumentException(
                sprintf(
                    'Expected an option value type to be "null|bool|string|string[]". Got "%s"',
                    get_debug_type($option),
                ),
            );
        }

        foreach ($option as $item) {
            self::assertIsValidOptionType($item);
        }
    }

    /**
     * @param mixed $argument
     *
     * @psalm-assert string|null $argument
     */
    public static function assertIsNotArray($argument): void
    {
        /** @psalm-suppress MissingClosureReturnType */
        self::castThrowException(
            static fn () => Assert::false(
                is_array($argument),
                sprintf(
                    'Cannot cast an array input argument as a scalar. Got the argument value: "%s"',
                    var_export($argument, true),
                ),
            ),
        );
    }

    /**
     * @param mixed $argument
     *
     * @psalm-assert array $argument
     */
    public static function assertIsArray($argument): void
    {
        /** @psalm-suppress MissingClosureReturnType */
        self::castThrowException(
            static fn () => Assert::isArray(
                $argument,
                sprintf(
                    'Cannot cast a non-array input argument into an array. Got the value "%s"',
                    var_export($argument, true),
                ),
            ),
        );
    }

    /**
     * @psalm-assert numeric $value
     *
     * @param mixed $argument
     */
    public static function numeric($argument): void
    {
        /** @psalm-suppress MissingClosureReturnType */
        self::castThrowException(
            static fn () => Assert::numeric(
                $argument,
                sprintf(
                    'Expected a numeric. Got "%s"',
                    var_export($argument, true),
                ),
            ),
        );
    }

    /**
     * @psalm-assert int $value
     *
     * @param mixed $argument
     */
    public static function integer($argument): void
    {
        /** @psalm-suppress MissingClosureReturnType */
        self::castThrowException(
            static fn () => Assert::digits(
                $argument,
                sprintf(
                    'Expected an integer. Got "%s"',
                    var_export($argument, true),
                ),
            ),
        );
    }

    /**
     * @param callable(): void $callable
     */
    private static function castThrowException(callable $callable): void
    {
        try {
            $callable();
        } catch (AssertInvalidArgumentException $exception) {
            throw new ConsoleInvalidArgumentException(
                $exception->getMessage(),
                (int) $exception->getCode(),
                $exception,
            );
        }
    }
}
