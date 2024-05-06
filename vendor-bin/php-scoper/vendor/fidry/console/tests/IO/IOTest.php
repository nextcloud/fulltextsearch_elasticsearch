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
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Console\Exception\InvalidArgumentException as ConsoleInvalidArgumentException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers \Fidry\Console\Command\ConsoleAssert
 * @covers \Fidry\Console\IO
 */
final class IOTest extends TestCase
{
    public function test_it_exposes_its_input_and_output(): void
    {
        $input = new StringInput('');
        $output = new NullOutput();

        $io = new IO($input, $output);

        self::assertSame($input, $io->getInput());
        self::assertSame($output, $io->getOutput());
    }

    /**
     * @dataProvider inputProvider
     */
    public function test_it_exposes_if_its_input_is_interactive(
        InputInterface $input,
        bool $expectedInteractivity
    ): void {
        $output = new NullOutput();

        $io = new IO($input, $output);

        self::assertSame($expectedInteractivity, $io->isInteractive());
    }

    public static function inputProvider(): iterable
    {
        foreach ([true, false] as $interactive) {
            yield [
                self::createInput($interactive),
                $interactive,
            ];
        }
    }

    public function test_it_can_create_a_null_io(): void
    {
        $io = IO::createNull();

        self::assertCount(0, $io->getInput()->getArguments());
        self::assertCount(0, $io->getInput()->getOptions());

        self::assertInstanceOf(NullOutput::class, $io->getOutput());
    }

    /**
     * @dataProvider invalidScalarArgumentTypeProvider
     * @dataProvider invalidArrayArgumentTypeProvider
     *
     * @param mixed $default
     */
    public function test_it_checks_against_invalid_argument_default_types(
        $default,
        string $expectedMessage
    ): void {
        $io = new IO(
            new ArrayInput(
                [],
                new InputDefinition(
                    [
                        new InputArgument(
                            'arg',
                            InputArgument::OPTIONAL,
                            '',
                            $default,
                        ),
                    ],
                ),
            ),
            new NullOutput(),
        );

        $this->expectException(ConsoleInvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $io->getStringArgument('arg');
    }

    public static function invalidScalarArgumentTypeProvider(): iterable
    {
        yield 'boolean' => [
            false,
            'Expected an argument value type to be "null|string|string[]". Got "bool"',
        ];

        yield 'int' => [
            10,
            'Expected an argument value type to be "null|string|string[]". Got "int"',
        ];

        yield 'float' => [
            10.8,
            'Expected an argument value type to be "null|string|string[]". Got "float"',
        ];

        yield 'object' => [
            new stdClass(),
            'Expected an argument value type to be "null|string|string[]". Got "stdClass"',
        ];

        yield 'closure' => [
            static fn () => '',
            'Expected an argument value type to be "null|string|string[]". Got "Closure"',
        ];
    }

    public static function invalidArrayArgumentTypeProvider(): iterable
    {
        foreach (self::invalidScalarArgumentTypeProvider() as [$item, $message]) {
            yield [[$item], $message];
        }
    }

    /**
     * @dataProvider invalidScalarOptionTypeProvider
     * @dataProvider invalidArrayOptionTypeProvider
     *
     * @param mixed $default
     */
    public function test_it_checks_against_invalid_option_default_types(
        $default,
        string $expectedMessage
    ): void {
        $io = new IO(
            new ArrayInput(
                [],
                new InputDefinition(
                    [
                        new InputOption(
                            'opt',
                            null,
                            InputOption::VALUE_OPTIONAL,
                            '',
                            $default,
                        ),
                    ],
                ),
            ),
            new NullOutput(),
        );

        $this->expectException(ConsoleInvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $io->getStringOption('opt');
    }

    public static function invalidScalarOptionTypeProvider(): iterable
    {
        yield 'int' => [
            10,
            'Expected an option value type to be "null|bool|string|string[]". Got "int"',
        ];

        yield 'float' => [
            10.8,
            'Expected an option value type to be "null|bool|string|string[]". Got "float"',
        ];

        yield 'object' => [
            new stdClass(),
            'Expected an option value type to be "null|bool|string|string[]". Got "stdClass"',
        ];

        yield 'closure' => [
            static fn () => '',
            'Expected an option value type to be "null|bool|string|string[]". Got "Closure"',
        ];
    }

    public static function invalidArrayOptionTypeProvider(): iterable
    {
        foreach (self::invalidScalarOptionTypeProvider() as [$item, $message]) {
            yield [[$item], $message];
        }
    }

    private static function createInput(bool $interactive): InputInterface
    {
        $input = new StringInput('');
        $input->setInteractive($interactive);

        return $input;
    }
}
