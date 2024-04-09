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

namespace Fidry\Console\Tests\Command;

use Fidry\Console\Command\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @covers \Fidry\Console\Command\Configuration
 */
final class ConfigurationTest extends TestCase
{
    public function test_it_can_be_instantiated_with_minimum_params(): void
    {
        $configuration = new Configuration(
            $name = 'app:foo',
            $description = 'description',
            $help = 'help',
        );

        self::assertStateIs(
            $configuration,
            $name,
            $description,
            $help,
            [],
            [],
        );
    }

    public function test_it_can_be_instantiated_without_arguments_or_options(): void
    {
        $configuration = new Configuration(
            $name = 'app:foo',
            $description = 'description',
            $help = 'help',
            $arguments = [],
            $options = [],
        );

        self::assertStateIs(
            $configuration,
            $name,
            $description,
            $help,
            $arguments,
            $options,
        );
    }

    public function test_it_can_be_instantiated_with_arguments_or_options(): void
    {
        $configuration = new Configuration(
            $name = 'app:foo',
            $description = 'description',
            $help = 'help',
            $arguments = [
                new InputArgument(
                    'arg',
                    InputArgument::REQUIRED,
                    'arg description',
                ),
            ],
            $options = [
                new InputOption(
                    'opt',
                    'o',
                    InputOption::VALUE_NONE,
                    'option description',
                ),
            ],
        );

        self::assertStateIs(
            $configuration,
            $name,
            $description,
            $help,
            $arguments,
            $options,
        );
    }

    private static function assertStateIs(
        Configuration $configuration,
        string $expectedName,
        string $expectedDescription,
        string $expectedHelp,
        array $expectedArguments,
        array $expectedOptions
    ): void {
        self::assertSame($expectedName, $configuration->getName());
        self::assertSame($expectedDescription, $configuration->getDescription());
        self::assertSame($expectedHelp, $configuration->getHelp());
        self::assertEquals($expectedArguments, $configuration->getArguments());
        self::assertEquals($expectedOptions, $configuration->getOptions());
    }
}
