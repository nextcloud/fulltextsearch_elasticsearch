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

use Fidry\Console\Command\SymfonyCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \Fidry\Console\Command\SymfonyCommand
 */
final class CommandHelperInjectionSupportTest extends KernelTestCase
{
    public function test_it_can_be_instantiated(): void
    {
        $kernel = self::bootKernel();

        $command = (new Application($kernel))->find('app:helpers');

        self::assertInstanceOf(SymfonyCommand::class, $command);
    }
}
