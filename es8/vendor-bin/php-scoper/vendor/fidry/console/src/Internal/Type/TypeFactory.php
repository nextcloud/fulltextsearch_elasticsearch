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

namespace Fidry\Console\Internal\Type;

use function array_reverse;

final class TypeFactory
{
    private function __construct()
    {
    }

    /**
     * @param non-empty-list<class-string<InputType>> $typeClassNames
     */
    public static function createTypeFromClassNames(array $typeClassNames): InputType
    {
        $args = [];

        foreach (array_reverse($typeClassNames) as $typeClassName) {
            $type = new $typeClassName(...$args);
            $args = [$type];
        }

        return $type;
    }
}
