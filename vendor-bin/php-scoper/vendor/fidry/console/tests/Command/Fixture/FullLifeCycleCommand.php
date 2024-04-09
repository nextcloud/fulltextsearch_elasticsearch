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

namespace Fidry\Console\Tests\Command\Fixture;

use Exception;
use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;
use Fidry\Console\Command\InitializableCommand;
use Fidry\Console\Command\InteractiveCommand;
use Fidry\Console\ExitCode;
use Fidry\Console\Helper\QuestionHelper;
use Fidry\Console\IO;
use LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;

/**
 * Most basic command: only has a name & description and does not do anything
 * during execution.
 */
final class FullLifeCycleCommand implements Command, InitializableCommand, InteractiveCommand
{
    private bool $initialized = false;
    private QuestionHelper $questionHelper;

    public function __construct(QuestionHelper $questionHelper)
    {
        $this->questionHelper = $questionHelper;
    }

    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'app:full-life-cycle',
            '',
            '',
            [
                new InputArgument(
                    'username',
                    InputArgument::REQUIRED,
                    'Name of the user',
                ),
            ],
        );
    }

    public function initialize(IO $io): void
    {
        $this->initialized = true;
    }

    public function interact(IO $io): void
    {
        if (!$this->initialized) {
            throw new LogicException('Expected the command to be initialized');
        }

        $input = $io->getInput();

        if ($input->getArgument('username')) {
            return;
        }

        $username = $this->askUsername($io);

        $input->setArgument('username', $username);
    }

    public function execute(IO $io): int
    {
        $io->newLine();
        $io->writeln(self::getUsername($io));

        return ExitCode::SUCCESS;
    }

    /** @psalm-suppress InvalidNullableReturnType, InvalidReturnStatement, InvalidReturnType, NullableReturnStatement */
    private static function getUsername(IO $io): string
    {
        return $io->getInput()->getArgument('username');
    }

    private function askUsername(IO $io): string
    {
        $question = new Question('Please choose a username:');

        $question->setValidator(
            function (string $username) {
                if ('' === $username) {
                    throw new Exception('Username can not be empty');
                }

                return $username;
            },
        );

        return $this->questionHelper->ask($io, $question);
    }
}
