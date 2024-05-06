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

namespace Fidry\Console\Command;

use Fidry\Console\ExitCode;
use Fidry\Console\IO;

interface Command
{
    public function getConfiguration(): Configuration;

    /**
     * Executes the current command.
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @see ExitCode
     */
    public function execute(IO $io): int;
}
