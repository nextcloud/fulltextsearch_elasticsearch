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
use Fidry\Console\Tests\StatefulService;

final class CommandWithService implements Command
{
    private StatefulService $service;

    public function __construct(StatefulService $service)
    {
        $this->service = $service;
    }

    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'app:with-service',
            '',
            '',
        );
    }

    public function execute(IO $io): int
    {
        $this->service->call();

        return ExitCode::SUCCESS;
    }
}
