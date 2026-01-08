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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as BaseSymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Bridge between a traditional Symfony console command and the new Command API.
 */
final class SymfonyCommand extends BaseSymfonyCommand
{
    private Command $command;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private IO $io;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private CommandRegistry $commandRegistry;

    public function __construct(Command $command)
    {
        $this->command = $command;

        $name = $command->getConfiguration()->getName();

        parent::__construct($name);
    }

    public function setApplication(?Application $application = null): void
    {
        parent::setApplication($application);

        if (null !== $application) {
            $this->commandRegistry = new CommandRegistry($application);
        }
    }

    protected function configure(): void
    {
        $configuration = $this->command->getConfiguration();

        $this
            ->setDescription($configuration->getDescription())
            ->setHelp($configuration->getHelp());

        $definition = $this->getDefinition();

        $definition->setArguments($configuration->getArguments());
        $definition->setOptions($configuration->getOptions());
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new IO($input, $output);

        $command = $this->command;

        if ($command instanceof CommandAware) {
            $command->setCommandRegistry($this->commandRegistry);
        }

        if ($command instanceof InitializableCommand) {
            $command->initialize($this->io);
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $command = $this->command;

        if ($command instanceof InteractiveCommand) {
            $command->interact($this->io);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->command->execute($this->io);
    }
}
