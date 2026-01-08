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
use function str_replace;
use const PHP_EOL;

/**
 * Utility class which helps to compare command outputs.
 */
final class DisplayNormalizer
{
    private function __construct()
    {
    }

    /**
     * @param callable(string):string $extraNormalizers
     */
    public static function removeTrailingSpaces(
        string $display,
        callable ...$extraNormalizers
    ): string {
        $display = str_replace(PHP_EOL, "\n", $display);
        $lines = explode("\n", $display);

        $trimmedLines = array_map(
            'rtrim',
            $lines,
        );

        $normalizedDisplay = implode("\n", $trimmedLines);

        return array_reduce(
            $extraNormalizers,
            static fn (string $display, $extraNormalizer): string => $extraNormalizer($display),
            $normalizedDisplay,
        );
    }
}
