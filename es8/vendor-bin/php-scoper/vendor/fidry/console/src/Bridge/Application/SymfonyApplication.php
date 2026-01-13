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

namespace Fidry\Console\Bridge\Application;

use Fidry\Console\Application\Application;
use Fidry\Console\Application\ConfigurableIO;
use Fidry\Console\Bridge\Command\BasicSymfonyCommandFactory;
use Fidry\Console\Bridge\CommandLoader\CommandLoaderFactory;
use Fidry\Console\Bridge\CommandLoader\SymfonyFactoryCommandLoaderFactory;
use Fidry\Console\Deprecation;
use Fidry\Console\IO;
use LogicException;
use Symfony\Component\Console\Application as BaseSymfonyApplication;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Bridge to create a traditional Symfony application from the new Application
 * API.
 */
final class SymfonyApplication extends BaseSymfonyApplication
{
    public function __construct(
        private readonly Application $application,
        ?CommandLoaderFactory $commandLoaderFactory = null,
    ) {
        parent::__construct(
            $application->getName(),
            $application->getVersion(),
        );

        if (null === $commandLoaderFactory) {
            $commandLoaderFactory = new SymfonyFactoryCommandLoaderFactory(
                new BasicSymfonyCommandFactory(),
            );

            Deprecation::trigger(
                'The parameter "$commandLoaderFactory" will be made non-nullable in future versions.',
                '0.5',
            );
        }

        $this->setDefaultCommand($application->getDefaultCommand());
        $this->setAutoExit($application->isAutoExitEnabled());
        $this->setCatchExceptions($application->areExceptionsCaught());
        $this->setCommandLoader(
            $commandLoaderFactory->createCommandLoader($application->getCommands()),
        );
    }

    public function reset(): void
    {
        if ($this->application instanceof ResetInterface) {
            $this->application->reset();
        }
    }

    public function setHelperSet(HelperSet $helperSet): void
    {
        throw new LogicException('Not supported');
    }

    public function setDefinition(InputDefinition $definition): void
    {
        throw new LogicException('Not supported');
    }

    public function getHelp(): string
    {
        return $this->application->getHelp();
    }

    public function getLongVersion(): string
    {
        return $this->application->getLongVersion();
    }

    public function setSignalsToDispatchEvent(int ...$signalsToDispatchEvent): void
    {
        throw new LogicException('Not supported');
    }

    public function setName(string $name): void
    {
        throw new LogicException('Not supported');
    }

    public function setVersion(string $version): void
    {
        throw new LogicException('Not supported');
    }

    protected function configureIO(InputInterface $input, OutputInterface $output): void
    {
        parent::configureIO($input, $output);

        if ($this->application instanceof ConfigurableIO) {
            $this->application->configureIO(
                new IO($input, $output),
            );
        }
    }
}
