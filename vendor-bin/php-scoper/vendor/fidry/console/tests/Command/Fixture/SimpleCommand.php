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

/**
 * Most basic command: only has a name & description and does not do anything
 * during execution.
 */
final class SimpleCommand implements Command
{
    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'app:foo',
            'Description content',
            'Command name: "%command.name%", command full name: "%command.full_name%"',
        );
    }

    public function execute(IO $io): int
    {
        return ExitCode::SUCCESS;
    }
}
