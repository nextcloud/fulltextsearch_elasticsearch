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
use Fidry\Console\Command\CommandAware;
use Fidry\Console\Command\CommandAwareness;
use Fidry\Console\Command\Configuration;
use Fidry\Console\Command\InitializableCommand;
use Fidry\Console\ExitCode;
use Fidry\Console\IO;

final class CommandAwareCommand implements Command, CommandAware, InitializableCommand
{
    use CommandAwareness;

    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'app:cmd-aware',
            '',
            '',
        );
    }

    public function initialize(IO $io): void
    {
        $command = $this->getCommandRegistry()->getCommand('app:with-service');
        $command->execute($io);
    }

    public function execute(IO $io): int
    {
        return ExitCode::SUCCESS;
    }
}
