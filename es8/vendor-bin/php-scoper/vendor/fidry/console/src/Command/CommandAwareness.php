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

use LogicException;

/**
 * @psalm-require-implements CommandAware
 */
trait CommandAwareness
{
    private CommandRegistry $_commandRegistry;

    public function setCommandRegistry(CommandRegistry $commandRegistry): void
    {
        $this->_commandRegistry = $commandRegistry;
    }

    private function getCommandRegistry(): CommandRegistry
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->_commandRegistry)) {
            throw new LogicException('Expected the command registry to be configured');
        }

        return $this->_commandRegistry;
    }
}
