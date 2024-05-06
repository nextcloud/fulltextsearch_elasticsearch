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

namespace Fidry\Console\Tests\Application\Feature;

use Fidry\Console\Application\ApplicationRunner;
use Fidry\Console\Tests\Application\Fixture\SimpleApplication;
use Fidry\Console\Tests\Application\OutputAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Fidry\Console\Application\ApplicationRunner
 * @covers \Fidry\Console\Application\SymfonyApplication
 */
final class ApplicationSimpleConfigSupportTest extends TestCase
{
    public function test_it_can_show_the_list_of_the_available_commands(): void
    {
        $input = new StringInput('list');
        $output = new BufferedOutput();

        ApplicationRunner::runApplication(
            new SimpleApplication(),
            $input,
            $output,
        );

        $actual = $output->fetch();
        $expected = <<<'EOT'
            help message
            
            Usage:
              command [options] [arguments]
            
            Options:
              -h, --help            Display help for the given command. When no command is given display help for the app:foo command
              -q, --quiet           Do not output any message
              -V, --version         Display this application version
                  --ansi            Force ANSI output
                  --no-ansi         Disable ANSI output
              -n, --no-interaction  Do not ask any interactive question
              -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
            
            Available commands:
              help     Display help for a command
              list     List commands
             app
              app:foo  Description content

            EOT;

        OutputAssertions::assertSameOutput(
            $expected,
            $actual,
        );
    }

    public function test_it_executes_the_default_command_by_default(): void
    {
        $input = new StringInput('');
        $output = new BufferedOutput();

        ApplicationRunner::runApplication(
            new SimpleApplication(),
            $input,
            $output,
        );

        $actual = $output->fetch();
        OutputAssertions::assertSameOutput(
            '',
            $actual,
        );
    }

    public function test_it_can_show_its_version(): void
    {
        $input = new StringInput('--version');
        $output = new BufferedOutput();

        ApplicationRunner::runApplication(
            new SimpleApplication(),
            $input,
            $output,
        );

        $actual = $output->fetch();
        $expected = <<<'EOT'
            1.0.0@60a94d3e

            EOT;

        OutputAssertions::assertSameOutput(
            $expected,
            $actual,
        );
    }
}
