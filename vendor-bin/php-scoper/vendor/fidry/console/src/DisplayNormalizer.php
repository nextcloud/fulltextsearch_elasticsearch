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

namespace Fidry\Console;

use function array_map;
use function explode;
use function implode;
use const PHP_EOL;
use function str_replace;

/**
 * Utility class which helps to compare command outputs.
 */
final class DisplayNormalizer
{
    private function __construct()
    {
    }

    public static function removeTrailingSpaces(string $display): string
    {
        $display = str_replace(PHP_EOL, "\n", $display);
        $lines = explode("\n", $display);

        $lines = array_map(
            'rtrim',
            $lines
        );

        return implode("\n", $lines);
    }
}
