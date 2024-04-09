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
final class CommandArgumentsAndOptionsSupportTest extends KernelTestCase
{
    private Command $command;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->command = (new Application($kernel))->find('app:print-arg-opt');
        $this->tester = new CommandTester($this->command);
    }

    public function test_it_supports_the_command_meta_description(): void
    {
        CommandAssertions::assertSameMetaDescription(
            $this->command,
            'app:print-arg-opt',
            '',
            '',
            'app:print-arg-opt [-o|--opt] [--] <arg>',
        );
        self::assertSame('app:print-arg-opt', $this->command->getName());
    }

    public function test_it_can_be_executed_without_an_option(): void
    {
        $this->tester->execute(
            ['arg' => 'Hello world!'],
            ['interactive' => false],
        );

        CommandAssertions::assertSameOutput(
            $this->tester,
            ExitCode::SUCCESS,
            <<<'TEXT'
            arg: Hello world!; opt: false
            
            TEXT,
        );
    }

    public function test_it_can_be_executed_with_an_option(): void
    {
        $this->tester->execute(
            [
                'arg' => 'Hello world!',
                '--opt' => null,
            ],
            ['interactive' => false],
        );

        CommandAssertions::assertSameOutput(
            $this->tester,
            ExitCode::SUCCESS,
            <<<'TEXT'
            arg: Hello world!; opt: true
            
            TEXT,
        );
    }
}
