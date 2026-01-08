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

use Fidry\Console\Bridge\Command\ReversedSymfonyCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Utility class which allows to easily access to the known commands of a given
 * application.
 */
final class CommandRegistry
{
    private Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Returns a registered command by name or alias.
     *
     * @throws CommandNotFoundException When given command name does not exist
     */
    public function getCommand(string $name): Command
    {
        return new ReversedSymfonyCommand(
            $this->application->get($name),
        );
    }

    /**
     * Finds a command by name or alias.
     *
     * Contrary to get, this command tries to find the best
     * match if you give it an abbreviation of a name or alias.
     *
     * @throws CommandNotFoundException When command name is incorrect or ambiguous
     */
    public function findCommand(string $name): Command
    {
        return new ReversedSymfonyCommand(
            $this->application->find($name),
        );
    }
}
