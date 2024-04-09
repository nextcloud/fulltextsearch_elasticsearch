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

namespace Fidry\Console\Tests\Application;

use Fidry\Console\DisplayNormalizer;
use PHPUnit\Framework\Assert;

final class OutputAssertions
{
    private function __construct()
    {
    }

    public static function assertSameOutput(string $expected, string $actual): void
    {
        Assert::assertSame(
            DisplayNormalizer::removeTrailingSpaces($expected),
            DisplayNormalizer::removeTrailingSpaces($actual),
        );
    }
}
