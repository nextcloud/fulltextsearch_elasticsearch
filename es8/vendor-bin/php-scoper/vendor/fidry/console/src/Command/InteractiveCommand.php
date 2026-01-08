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

use Fidry\Console\IO;

interface InteractiveCommand extends Command
{
    /**
     * Its purpose is to check if some of the options/arguments are missing and
     * interactively ask the user for those values. This is the last place where
     * you can ask for missing options/arguments. After this command, missing
     * options/arguments will result in an error.
     *
     * Note that this method is ignored if the input is not interactive.
     */
    public function interact(IO $io): void;
}
