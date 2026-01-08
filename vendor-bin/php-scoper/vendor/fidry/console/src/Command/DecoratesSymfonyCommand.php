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

use Symfony\Component\Console\Command\Command as SymfonyCommand;

interface DecoratesSymfonyCommand extends Command
{
    /**
     * Returns the decorated inner Symfony command. This is useful for custom
     * commands that decorate a Symfony command that should still be "configured".
     *
     * Indeed, when executing a Symfony application, the application injects itself to
     * its commands which allows to merge its definition with the commands. As a result,
     * the commands inherit from options such as "--no-interaction" or "--no-debug".
     *
     * In order to allow this to happen when decorating the Symfony command, the inner
     * Symfony command needs to be exposed to allow the application to be injected.
     */
    public function getDecoratedCommand(): SymfonyCommand;
}
