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
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Fidry\Console\Command\SymfonyCommand
 */
final class CommandMetaDescriptionSupportTest extends KernelTestCase
{
    private Command $command;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->command = (new Application($kernel))->find('app:foo');
        $this->tester = new CommandTester($this->command);
    }

    public function test_it_supports_the_command_meta_description(): void
    {
        self::assertMatchesRegularExpression(
            '/^Command name: "app:foo", command full name: ".+ app:foo"$/',
            $this->command->getProcessedHelp(),
        );

        CommandAssertions::assertSameMetaDescription(
            $this->command,
            'app:foo',
            'Description content',
            $this->command->getProcessedHelp(),
            'app:foo',
        );
    }

    public function test_it_can_be_executed_non_interactively(): void
    {
        $this->tester->execute([], ['interactive' => false]);

        CommandAssertions::assertSameOutput(
            $this->tester,
            ExitCode::SUCCESS,
            '',
        );
    }

    public function test_it_can_be_executed_interactively(): void
    {
        $this->tester->execute([], ['interactive' => true]);

        CommandAssertions::assertSameOutput(
            $this->tester,
            ExitCode::SUCCESS,
            '',
        );
    }
}
