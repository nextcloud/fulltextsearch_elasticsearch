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
use Fidry\Console\Internal\Type\ListType;
use Fidry\Console\Internal\Type\NonEmptyListType;
use function array_slice;
use function in_array;

/**
 * @private
 */
final class TypeNameSorter
{
    private const INVERSE_TYPE_CLASS_NAMES = [
        ListType::class,
        NonEmptyListType::class,
    ];

    private function __construct()
    {
    }

    /**
     * @param list<class-string<InputType>> $typeClassNames
     *
     * @return list<class-string<InputType>>
     */
    public static function sortClassNames(array $typeClassNames): array
    {
        $sortedTypes = [];

        self::traverseAndCollectTypes($typeClassNames, $sortedTypes);

        return $sortedTypes;
    }

    /**
     * @param list<class-string<InputType>> $unsortedTypes
     * @param list<class-string<InputType>> $sortedTypes
     */
    private static function traverseAndCollectTypes(array $unsortedTypes, array &$sortedTypes): void
    {
        foreach ($unsortedTypes as $index => $unsortedType) {
            if (!in_array($unsortedType, self::INVERSE_TYPE_CLASS_NAMES, true)) {
                $sortedTypes[] = $unsortedType;

                continue;
            }

            $listInnerTypes = array_slice(
                $unsortedTypes,
                $index + 1,
            );

            self::traverseAndCollectTypes($listInnerTypes, $sortedTypes);
            $sortedTypes[] = $unsortedType;

            break;
        }
    }
}
