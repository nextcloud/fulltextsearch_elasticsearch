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
use Fidry\Console\Tests\Application\Fixture\ApplicationThrowingException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use UnexpectedValueException;

/**
 * @covers \Fidry\Console\Application\ApplicationRunner
 * @covers \Fidry\Console\Application\SymfonyApplication
 */
final class ApplicationCatchingExceptionSupportTest extends TestCase
{
    public function test_it_can_show_the_list_of_the_available_commands(): void
    {
        $input = new StringInput('app:fail');
        $output = new NullOutput();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Fail');

        ApplicationRunner::runApplication(
            new ApplicationThrowingException(),
            $input,
            $output,
        );
    }
}
