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

use Fidry\Console\Application\BaseApplication as FidryBaseApplication;
use Fidry\Console\Tests\Command\Fixture\SimpleCommand;

final class BaseApplication extends FidryBaseApplication
{
    public function getName(): string
    {
        return 'BaseApp';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getCommands(): array
    {
        return [
            new SimpleCommand(),
            new FailingCommand(),
        ];
    }

    public function isAutoExitEnabled(): bool
    {
        return false;
    }
}
