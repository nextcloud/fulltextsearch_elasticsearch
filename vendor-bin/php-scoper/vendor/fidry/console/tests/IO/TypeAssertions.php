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

namespace Fidry\Console\Tests\IO;

use Fidry\Console\IO;
use PHPUnit\Framework\Assert;
use function Safe\sprintf;
use Symfony\Component\Console\Exception\InvalidArgumentException as ConsoleInvalidArgumentException;

final class TypeAssertions
{
    private function __construct()
    {
    }

    public static function assertExpectedArgumentTypes(
        TypedInput $expected,
        IO $io,
        string $argumentName
    ): void {
        self::assertExpectedType(
            $expected->boolean,
            static fn () => $io->getBooleanArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->nullableBoolean,
            static fn () => $io->getNullableBooleanArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->string,
            static fn () => $io->getStringArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->nullableString,
            static fn () => $io->getNullableStringArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->stringArray,
            static fn () => $io->getStringArrayArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->integer,
            static fn () => $io->getIntegerArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->nullableInteger,
            static fn () => $io->getNullableIntegerArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->integerArray,
            static fn () => $io->getIntegerArrayArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->float,
            static fn () => $io->getFloatArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->nullableFloat,
            static fn () => $io->getNullableFloatArgument($argumentName),
        );
        self::assertExpectedType(
            $expected->floatArray,
            static fn () => $io->getFloatArrayArgument($argumentName),
        );
    }

    public static function assertExpectedOptionTypes(
        TypedInput $expected,
        IO $io,
        string $optionName
    ): void {
        self::assertExpectedType(
            $expected->boolean,
            static fn () => $io->getBooleanOption($optionName),
        );
        self::assertExpectedType(
            $expected->nullableBoolean,
            static fn () => $io->getNullableBooleanOption($optionName),
        );
        self::assertExpectedType(
            $expected->string,
            static fn () => $io->getStringOption($optionName),
        );
        self::assertExpectedType(
            $expected->nullableString,
            static fn () => $io->getNullableStringOption($optionName),
        );
        self::assertExpectedType(
            $expected->stringArray,
            static fn () => $io->getStringArrayOption($optionName),
        );
        self::assertExpectedType(
            $expected->integer,
            static fn () => $io->getIntegerOption($optionName),
        );
        self::assertExpectedType(
            $expected->nullableInteger,
            static fn () => $io->getNullableIntegerOption($optionName),
        );
        self::assertExpectedType(
            $expected->integerArray,
            static fn () => $io->getIntegerArrayOption($optionName),
        );
        self::assertExpectedType(
            $expected->float,
            static fn () => $io->getFloatOption($optionName),
        );
        self::assertExpectedType(
            $expected->nullableFloat,
            static fn () => $io->getNullableFloatOption($optionName),
        );
        self::assertExpectedType(
            $expected->floatArray,
            static fn () => $io->getFloatArrayOption($optionName),
        );
    }

    /**
     * @param mixed|TypeException $expected
     * @param callable():mixed    $getArgument
     */
    private static function assertExpectedType($expected, callable $getArgument): void
    {
        try {
            $actual = $getArgument();

            if ($expected instanceof TypeException) {
                Assert::fail(
                    sprintf(
                        'Expected a type exception to be thrown with the message "%s"',
                        $expected->message,
                    ),
                );
            }
        } catch (ConsoleInvalidArgumentException $exception) {
            if ($expected instanceof TypeException) {
                Assert::assertSame(
                    $expected->message,
                    $exception->getMessage(),
                );

                return;
            }

            throw $exception;
        }

        Assert::assertSame($expected, $actual);
    }
}
