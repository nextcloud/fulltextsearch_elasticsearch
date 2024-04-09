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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers \Fidry\Console\Command\ConsoleAssert
 * @covers \Fidry\Console\IO
 */
final class IOOptionsTest extends TestCase
{
    private const OPTION_NAME = 'opt';

    /**
     * @dataProvider requiredOptionProvider
     * @dataProvider optionalOptionProvider
     * @dataProvider noValueOptionProvider
     * @dataProvider arrayOptionProvider
     */
    public function test_it_exposes_a_typed_api(
        InputOption $inputOption,
        string $option,
        TypedInput $expected
    ): void {
        $io = $this->getIO($inputOption, $option);

        TypeAssertions::assertExpectedOptionTypes(
            $expected,
            $io,
        self::OPTION_NAME,
        );
    }

    public static function requiredOptionProvider(): iterable
    {
        $mode = InputOption::VALUE_REQUIRED;

        yield 'empty string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=""',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "\'\'"'),
                false,
                false,
                '',
                '',
                new TypeException('Expected an integer. Got "\'\'"'),
                new TypeException('Expected an integer. Got "\'\'"'),
                new TypeException('Expected a numeric. Got "\'\'"'),
                new TypeException('Expected a numeric. Got "\'\'"'),
            ),
        ];

        yield 'nominal string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=foo',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "\'foo\'"'),
                true,
                true,
                'foo',
                'foo',
                new TypeException('Expected an integer. Got "\'foo\'"'),
                new TypeException('Expected an integer. Got "\'foo\'"'),
                new TypeException('Expected a numeric. Got "\'foo\'"'),
                new TypeException('Expected a numeric. Got "\'foo\'"'),
            ),
        ];

        yield 'null string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=null',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "\'null\'"'),
                true,
                true,
                'null',
                'null',
                new TypeException('Expected an integer. Got "\'null\'"'),
                new TypeException('Expected an integer. Got "\'null\'"'),
                new TypeException('Expected a numeric. Got "\'null\'"'),
                new TypeException('Expected a numeric. Got "\'null\'"'),
            ),
        ];

        yield 'integer string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=10',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "\'10\'"'),
                true,
                true,
                '10',
                '10',
                10,
                10,
                10.,
                10.,
            ),
        ];

        // negative integer string case: skipped see https://github.com/symfony/symfony/issues/27333

        yield 'zero integer string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=0',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "\'0\'"'),
                false,
                false,
                '0',
                '0',
                0,
                0,
                0.,
                0.,
            ),
        ];

        yield 'float string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=10.8',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "\'10.8\'"'),
                true,
                true,
                '10.8',
                '10.8',
                new TypeException('Expected an integer. Got "\'10.8\'"'),
                new TypeException('Expected an integer. Got "\'10.8\'"'),
                10.8,
                10.8,
            ),
        ];

        // negative float string case: skipped see https://github.com/symfony/symfony/issues/27333

        yield 'zero float string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=0.',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "\'0.\'"'),
                true,
                true,
                '0.',
                '0.',
                new TypeException('Expected an integer. Got "\'0.\'"'),
                new TypeException('Expected an integer. Got "\'0.\'"'),
                0.,
                0.,
            ),
        ];
    }

    public static function optionalOptionProvider(): iterable
    {
        yield 'empty string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                InputOption::VALUE_OPTIONAL,
                '',
                null,
            ),
            '',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "NULL"'),
                false,
                null,
                '',
                null,
                new TypeException('Expected an integer. Got "NULL"'),
                null,
                new TypeException('Expected a numeric. Got "NULL"'),
                null,
            ),
        ];
    }

    public static function noValueOptionProvider(): iterable
    {
        $mode = InputOption::VALUE_NONE;

        yield 'option absent' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,   // is ignored for VALUE_NONE (must be NULL)
            ),
            '',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "false"'),
                false,
                false,
                '',
                '',
                new TypeException('Expected an integer. Got "false"'),
                new TypeException('Expected an integer. Got "false"'),
                new TypeException('Expected a numeric. Got "false"'),
                new TypeException('Expected a numeric. Got "false"'),
            ),
        ];

        yield 'option present' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt',
            TypedInput::createForScalar(
                new TypeException('Cannot cast a non-array input argument into an array. Got the value "true"'),
                true,
                true,
                '1',
                '1',
                new TypeException('Expected an integer. Got "true"'),
                new TypeException('Expected an integer. Got "true"'),
                new TypeException('Expected a numeric. Got "true"'),
                new TypeException('Expected a numeric. Got "true"'),
            ),
        ];
    }

    public static function arrayOptionProvider(): iterable
    {
        $mode = InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY;

        yield 'empty string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=""',
            TypedInput::createForArray(
                new TypeException(
                    <<<'TXT'
                    Cannot cast an array input argument as a scalar. Got the argument value: "array (
                      0 => '',
                    )"
                    TXT,
                ),
                [''],
                new TypeException('Expected an integer. Got "\'\'"'),
                new TypeException('Expected a numeric. Got "\'\'"'),
            ),
        ];

        yield 'single element string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=foo',
            TypedInput::createForArray(
                new TypeException(
                    <<<'TXT'
                    Cannot cast an array input argument as a scalar. Got the argument value: "array (
                      0 => 'foo',
                    )"
                    TXT,
                ),
                ['foo'],
                new TypeException('Expected an integer. Got "\'foo\'"'),
                new TypeException('Expected a numeric. Got "\'foo\'"'),
            ),
        ];

        yield 'multiple elements string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=foo --opt=bar --opt=baz',
            TypedInput::createForArray(
                new TypeException(
                    <<<'TXT'
                    Cannot cast an array input argument as a scalar. Got the argument value: "array (
                      0 => 'foo',
                      1 => 'bar',
                      2 => 'baz',
                    )"
                    TXT,
                ),
                ['foo', 'bar', 'baz'],
                new TypeException('Expected an integer. Got "\'foo\'"'),
                new TypeException('Expected a numeric. Got "\'foo\'"'),
            ),
        ];

        yield 'null string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=null',
            TypedInput::createForArray(
                new TypeException(
                    <<<'TXT'
                    Cannot cast an array input argument as a scalar. Got the argument value: "array (
                      0 => 'null',
                    )"
                    TXT,
                ),
                ['null'],
                new TypeException('Expected an integer. Got "\'null\'"'),
                new TypeException('Expected a numeric. Got "\'null\'"'),
            ),
        ];

        yield 'integer string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=10',
            TypedInput::createForArray(
                new TypeException(
                    <<<'TXT'
                    Cannot cast an array input argument as a scalar. Got the argument value: "array (
                      0 => '10',
                    )"
                    TXT,
                ),
                ['10'],
                [10],
                [10.],
            ),
        ];

        // negative integer string case: skipped see https://github.com/symfony/symfony/issues/27333

        yield 'zero integer string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=0',
            TypedInput::createForArray(
                new TypeException(
                    <<<'TXT'
                    Cannot cast an array input argument as a scalar. Got the argument value: "array (
                      0 => '0',
                    )"
                    TXT,
                ),
                ['0'],
                [0],
                [0.],
            ),
        ];

        yield 'float string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=10.8',
            TypedInput::createForArray(
                new TypeException(
                    <<<'TXT'
                    Cannot cast an array input argument as a scalar. Got the argument value: "array (
                      0 => '10.8',
                    )"
                    TXT,
                ),
                ['10.8'],
                new TypeException('Expected an integer. Got "\'10.8\'"'),
                [10.8],
            ),
        ];

        // negative float string case: skipped see https://github.com/symfony/symfony/issues/27333

        yield 'zero float string' => [
            new InputOption(
                self::OPTION_NAME,
                null,
                $mode,
                '',
                null,
            ),
            '--opt=0.',
            TypedInput::createForArray(
                new TypeException(
                    <<<'TXT'
                    Cannot cast an array input argument as a scalar. Got the argument value: "array (
                      0 => '0.',
                    )"
                    TXT,
                ),
                ['0.'],
                new TypeException('Expected an integer. Got "\'0.\'"'),
                [0.],
            ),
        ];
    }

    private function getIO(
        InputOption $inputOption,
        string $option
    ): IO {
        $application = new Application();
        $application->add(
            new DynamicCommandWithOptions($inputOption),
        );

        $input = new StringInput('app:input:opts '.$option);
        $input->setInteractive(false);

        $application->doRun(
            $input,
            new NullOutput(),
        );

        $command = $application->find('app:input:opts');
        self::assertInstanceOf(DynamicCommandWithOptions::class, $command);

        return new IO(
            $command->input,
            new NullOutput(),
        );
    }
}
