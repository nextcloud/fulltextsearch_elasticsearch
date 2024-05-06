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
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Fidry\Console\Command\SymfonyCommand
 */
final class CommandLazinessSupportTest extends KernelTestCase
{
    private StatefulService $service;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->service = self::$kernel->getContainer()->get(StatefulService::class);
    }

    public function test_it_is_instantiated_lazily(): void
    {
        // Sanity check
        self::assertFalse($this->service->called);

        // Finding another command – if command is not lazy it will be loaded
        (new Application(self::$kernel))->find('app:foo');

        /** @psalm-suppress RedundantConditionGivenDocblockType */
        self::assertFalse($this->service->called);

        (new Application(self::$kernel))->find('app:lazy');

        /** @psalm-suppress DocblockTypeContradiction */
        self::assertTrue($this->service->called);
    }

    public function test_it_can_be_executed(): void
    {
        // Sanity check
        self::assertFalse($this->service->called);

        $command = (new Application(self::$kernel))->find('app:lazy');
        $tester = new CommandTester($command);

        $tester->execute([], ['interactive' => false]);

        /** @psalm-suppress DocblockTypeContradiction */
        self::assertTrue($this->service->called);
    }
}
