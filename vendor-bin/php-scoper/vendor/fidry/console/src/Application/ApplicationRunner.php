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

use Fidry\Console\IO;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class ApplicationRunner
{
    private SymfonyApplication $application;

    public function __construct(Application $application)
    {
        $this->application = new SymfonyApplication($application);
    }

    /**
     * Executes the given application command.
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @see ExitCode
     */
    public static function runApplication(
        Application $application,
        ?InputInterface $input,
        ?OutputInterface $output
    ): int {
        return (new self($application))->run(
            new IO(
                $input ?? new ArgvInput(),
                $output ?? new ConsoleOutput(),
            ),
        );
    }

    /**
     * @see ApplicationRunner::runApplication()
     */
    public function run(?IO $io = null): int
    {
        if (null === $io) {
            $input = null;
            $output = null;
        } else {
            $input = $io->getInput();
            $output = $io->getOutput();
        }

        return $this->application->run($input, $output);
    }
}
