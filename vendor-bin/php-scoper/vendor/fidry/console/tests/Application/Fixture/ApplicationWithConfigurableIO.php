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

namespace Fidry\Console\Tests\Application\Fixture;

use Fidry\Console\Application\Application;
use Fidry\Console\Application\ConfigurableIO;
use Fidry\Console\IO;
use Fidry\Console\Tests\Command\Fixture\SimpleCommand;
use Fidry\Console\Tests\StatefulService;

final class ApplicationWithConfigurableIO implements Application, ConfigurableIO
{
    private StatefulService $service;

    public function __construct(StatefulService $service)
    {
        $this->service = $service;
    }

    public function getName(): string
    {
        return 'App';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getLongVersion(): string
    {
        return '1.0.0@60a94d3e';
    }

    public function getCommands(): array
    {
        return [
            new SimpleCommand(),
        ];
    }

    public function isAutoExitEnabled(): bool
    {
        return false;
    }

    public function getHelp(): string
    {
        return 'help message';
    }

    public function getDefaultCommand(): string
    {
        return 'app:foo';
    }

    public function areExceptionsCaught(): bool
    {
        return true;
    }

    public function configureIO(IO $io): void
    {
        $this->service->call();
    }
}
