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

use Fidry\Console\Tests\StatefulService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Fidry\Console\Command\SymfonyCommand
 */
final class CommandServiceInjectionSupportTest extends KernelTestCase
{
    private Command $command;
    private CommandTester $tester;
    private StatefulService $service;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->command = (new Application($kernel))->find('app:with-service');
        $this->tester = new CommandTester($this->command);

        $this->service = $kernel->getContainer()->get(StatefulService::class);
    }

    public function test_it_can_be_executed(): void
    {
        // Sanity check
        self::assertFalse($this->service->called);

        $this->tester->execute([], ['interactive' => false]);

        /** @psalm-suppress DocblockTypeContradiction */
        self::assertTrue($this->service->called);
    }
}
