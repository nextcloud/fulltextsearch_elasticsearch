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

namespace Fidry\Console\Internal\Generator;

use Fidry\Console\Internal\Type\InputType;
use function array_map;
use function array_pop;
use function explode;
use function implode;

/**
 * @private
 */
final class GettersGenerator
{
    /**
     * @param non-empty-list<InputType> $types
     */
    public static function generate(array $types): string
    {
        $getters = [];

        foreach ($types as $type) {
            $getters[] = self::indentGetter(
                GetterGenerator::generate($type),
            );
            $getters[] = '';
        }

        array_pop($getters);

        $content = implode(
            "\n",
            $getters,
        );

        return self::trimTrailingSpaces($content);
    }

    private static function indentGetter(string $getter): string
    {
        $getterLines = explode("\n", $getter);

        $indentedGetterLines = array_map(
            static fn (string $getter) => '    '.$getter,
            $getterLines,
        );

        return implode("\n", $indentedGetterLines);
    }

    private static function trimTrailingSpaces(string $content): string
    {
        $lines = explode("\n", $content);

        $trimmedLines = array_map('rtrim', $lines);

        return implode("\n", $trimmedLines);
    }
}
