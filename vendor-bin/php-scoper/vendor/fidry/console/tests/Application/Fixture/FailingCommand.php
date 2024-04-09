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

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;
use Fidry\Console\IO;
use UnexpectedValueException;

final class FailingCommand implements Command
{
    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'app:fail',
            '',
            '',
        );
    }

    public function execute(IO $io): int
    {
        throw new UnexpectedValueException('Fail');
    }
}
