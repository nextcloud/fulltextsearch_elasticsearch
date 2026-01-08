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

namespace Fidry\Console\Application;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\LazyCommandEnvelope;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

interface Application
{
    /**
     * Name of the application. Typically, shown when running the application
     * or displaying the current version.
     */
    public function getName(): string;

    /**
     * Version of the application.
     */
    public function getVersion(): string;

    /**
     * Usually a combination of the name and version, typically displayed at
     * the beginning when executing a command.
     */
    public function getLongVersion(): string;

    /**
     * Message usually show when showing the description of a command.
     */
    public function getHelp(): string;

    /**
     * Exhaustive list of the custom commands. A few more commands such as
     * the HelpCommand or ListCommand are also included besides those.
     *
     * @return array<LazyCommandEnvelope|Command|SymfonyCommand>
     */
    public function getCommands(): array;

    /**
     * The default command executed when the application is being run without
     * any command specified. Typically, the list command which will display all
     * the available commands.
     */
    public function getDefaultCommand(): string;

    /**
     * When enabled the process will stop with the returned exit code. Otherwise
     * will simply return the exit code.
     */
    public function isAutoExitEnabled(): bool;

    /**
     * Whether exceptions are caught when executing the application. If yes a CLI
     * renderer will render the exception to the output, otherwise the exception
     * will be left alone and how the exception is rendered is left to the callee
     * (e.g. the regular PHP exception printer).
     */
    public function areExceptionsCaught(): bool;
}
