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

namespace Fidry\Console\Tests\Command;

use PHPUnit\Framework\Assert;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CommandAssertions
{
    private function __construct()
    {
    }

    public static function assertSameMetaDescription(
        Command $command,
        string $expectedName,
        string $expectedDescription,
        string $expectedHelp,
        string $expectedSynopsis
    ): void {
        Assert::assertSame($expectedName, $command->getName());
        Assert::assertSame($expectedDescription, $command->getDescription());
        Assert::assertSame($expectedHelp, $command->getProcessedHelp());
        Assert::assertSame($expectedSynopsis, $command->getSynopsis());
    }

    public static function assertSameOutput(
        CommandTester $executedCommand,
        int $expectedExitCode,
        string $expectedDisplay
    ): void {
        Assert::assertSame($expectedExitCode, $executedCommand->getStatusCode());
        Assert::assertSame($expectedDisplay, $executedCommand->getDisplay(true));
    }
}
