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

final class ApplicationThrowingException extends FidryBaseApplication
{
    public function getName(): string
    {
        return 'FailingApp';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getCommands(): array
    {
        return [
            new FailingCommand(),
        ];
    }

    public function isAutoExitEnabled(): bool
    {
        return false;
    }

    public function areExceptionsCaught(): bool
    {
        return false;
    }
}
