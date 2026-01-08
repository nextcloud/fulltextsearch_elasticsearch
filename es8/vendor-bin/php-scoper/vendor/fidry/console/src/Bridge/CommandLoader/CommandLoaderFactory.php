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

namespace Fidry\Console\Bridge\CommandLoader;

use Fidry\Console\Command\Command as FidryCommand;
use Fidry\Console\Command\LazyCommandEnvelope;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;

interface CommandLoaderFactory
{
    /**
     * @param array<LazyCommandEnvelope|FidryCommand|SymfonyCommand> $commands
     */
    public function createCommandLoader(array $commands): CommandLoaderInterface;
}
