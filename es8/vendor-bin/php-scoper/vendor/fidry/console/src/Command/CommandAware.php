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

/**
 * Should be implemented by commands which requires access to other registered
 * commands.
 *
 * @see CommandAwareness
 */
interface CommandAware extends Command
{
    public function setCommandRegistry(CommandRegistry $commandRegistry): void;
}
