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

namespace Fidry\Console\Tests\Command\Fixture;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;
use Fidry\Console\ExitCode;
use Fidry\Console\IO;
use function Safe\sprintf;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * A simple command with a required argument and displaying the argument value
 * and option during its execution.
 */
final class CommandWithArgumentAndOption implements Command
{
    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'app:print-arg-opt',
            '',
            '',
            [
                new InputArgument(
                    'arg',
                    InputArgument::REQUIRED,
                    'An arbitrary string',
                ),
            ],
            [
                new InputOption(
                    'opt',
                    'o',
                    InputOption::VALUE_NONE,
                ),
            ],
        );
    }

    public function execute(IO $io): int
    {
        $arg = self::getArg($io);
        $opt = self::getOpt($io);

        $io->writeln(
            sprintf(
                'arg: %s; opt: %s',
                $arg,
                $opt ? 'true' : 'false',
            ),
        );

        return ExitCode::SUCCESS;
    }

    /** @psalm-suppress InvalidNullableReturnType, InvalidReturnStatement, InvalidReturnType, NullableReturnStatement */
    private static function getArg(IO $io): string
    {
        return $io->getInput()->getArgument('arg');
    }

    /** @psalm-suppress InvalidNullableReturnType, InvalidReturnStatement, InvalidReturnType, NullableReturnStatement */
    private static function getOpt(IO $io): bool
    {
        return $io->getInput()->getOption('opt');
    }
}
