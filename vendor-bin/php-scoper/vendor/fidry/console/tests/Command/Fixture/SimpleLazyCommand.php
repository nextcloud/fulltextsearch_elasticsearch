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

use Fidry\Console\Command\Configuration;
use Fidry\Console\Command\LazyCommand;
use Fidry\Console\ExitCode;
use Fidry\Console\IO;
use Fidry\Console\Tests\StatefulService;

final class SimpleLazyCommand implements LazyCommand
{
    public function __construct(StatefulService $service)
    {
        $service->call();
    }

    public static function getName(): string
    {
        return 'app:lazy';
    }

    public function getConfiguration(): Configuration
    {
        return new Configuration(
            self::getName(),    // TODO: add test to check that the lazy name takes over
            '',
            '',
        );
    }

    public function execute(IO $io): int
    {
        return ExitCode::SUCCESS;
    }
}
