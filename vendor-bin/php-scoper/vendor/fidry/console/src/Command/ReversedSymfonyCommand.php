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

use Fidry\Console\Input\IO;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Bridge between a new Command API and a traditional Symfony console command.
 *
 * @private
 */
final class ReversedSymfonyCommand implements Command
{
    private SymfonyCommand $command;

    public function __construct(SymfonyCommand $command)
    {
        $this->command = $command;
    }

    public function getConfiguration(): Configuration
    {
        return new Configuration(
            $this->command->getName() ?? '',
            $this->command->getDescription(),
            $this->command->getHelp(),
            $this->command->getDefinition()->getArguments(),
            $this->command->getDefinition()->getOptions(),
        );
    }

    public function execute(IO $io): int
    {
        return $this->command->run(
            $io->getInput(),
            $io->getOutput(),
        );
    }
}
