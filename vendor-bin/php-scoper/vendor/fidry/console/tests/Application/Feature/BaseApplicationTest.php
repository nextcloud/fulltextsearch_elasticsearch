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
use Fidry\Console\IO;
use Fidry\Console\Tests\Application\Fixture\BaseApplication;
use Fidry\Console\Tests\Application\OutputAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Fidry\Console\Application\ApplicationRunner
 * @covers \Fidry\Console\Application\BaseApplication
 * @covers \Fidry\Console\Application\SymfonyApplication
 */
final class BaseApplicationTest extends TestCase
{
    private const EXPECTED = <<<'EOT'
    BaseApp 1.0.0
    
    Usage:
      command [options] [arguments]
    
    Options:
      -h, --help            Display help for the given command. When no command is given display help for the list command
      -q, --quiet           Do not output any message
      -V, --version         Display this application version
          --ansi            Force ANSI output
          --no-ansi         Disable ANSI output
      -n, --no-interaction  Do not ask any interactive question
      -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
    
    Available commands:
      help      Display help for a command
      list      List commands
     app
      app:fail
      app:foo   Description content

    EOT;

    public function test_it_can_be_run(): void
    {
        $input = new StringInput('list');
        $output = new BufferedOutput();

        ApplicationRunner::runApplication(
            new BaseApplication(),
            $input,
            $output,
        );

        OutputAssertions::assertSameOutput(
            self::EXPECTED,
            $output->fetch(),
        );
    }

    public function test_it_can_be_run_without_the_static_helper(): void
    {
        $input = new StringInput('list');
        $output = new BufferedOutput();

        $runner = new ApplicationRunner(new BaseApplication());

        $runner->run(
            new IO($input, $output),
        );

        OutputAssertions::assertSameOutput(
            self::EXPECTED,
            $output->fetch(),
        );
    }

    public function test_it_catches_exceptions_thrown(): void
    {
        $input = new StringInput('app:fail');
        $output = new BufferedOutput();

        ApplicationRunner::runApplication(
            new BaseApplication(),
            $input,
            $output,
        );

        OutputAssertions::assertSameOutput(
            <<<'EOT'
            
            In FailingCommand.php line 34:
            
              Fail
            
            
            app:fail
            
            
            EOT,
            $output->fetch(),
        );
    }
}
