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
use function array_unshift;
use function implode;

/**
 * @private
 */
final class GetterNameGenerator
{
    private function __construct()
    {
    }

    /**
     * @param list<class-string<InputType>> $typeClassNames
     */
    public static function generateMethodName(array $typeClassNames): string
    {
        $typeParts = array_map(
            static fn (string $typeClassName) => self::normalizeTypeName($typeClassName),
            TypeNameSorter::sortClassNames($typeClassNames),
        );

        array_unshift($typeParts, 'as');

        return implode('', $typeParts);
    }

    /**
     * @param class-string<InputType> $typeClassName
     */
    private static function normalizeTypeName(string $typeClassName): string
    {
        return mb_substr(
            ClassName::getShortClassName($typeClassName),
            0,
            -4,
        );
    }
}
