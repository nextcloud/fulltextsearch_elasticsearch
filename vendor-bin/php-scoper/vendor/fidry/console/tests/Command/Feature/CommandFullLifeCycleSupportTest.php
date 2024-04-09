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

namespace Fidry\Console\Tests\Command\Feature;

use Fidry\Console\ExitCode;
use Fidry\Console\Tests\Command\CommandAssertions;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Fidry\Console\Command\SymfonyCommand
 */
final class CommandFullLifeCycleSupportTest extends KernelTestCase
{
    private Command $command;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->command = (new Application($kernel))->find('app:full-life-cycle');
        $this->tester = new CommandTester($this->command);
    }

    public function test_it_can_be_executed(): void
    {
        $this->tester->setInputs(['username' => 'Jean']);

        $this->tester->execute([], ['interactive' => true]);

        CommandAssertions::assertSameOutput(
            $this->tester,
            ExitCode::SUCCESS,
            <<<'TEXT'
            Please choose a username:
            Jean

            TEXT,
        );
    }

    public function test_it_ignores_interact_if_input_is_not_interactive(): void
    {
        $this->tester->setInputs(['username' => 'Jean']);

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Not enough arguments (missing: "username")');

        $this->tester->execute([], ['interactive' => false]);
    }
}
