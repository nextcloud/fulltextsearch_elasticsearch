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

use function mb_strrpos;

/**
 * @private
 */
final class ClassName
{
    private function __construct()
    {
    }

    /**
     * Strips the namespace off a fully-qualified class name. E.g.:
     * "Acme\Foo\Bar" -> "Bar".
     *
     * @param class-string $className
     */
    public static function getShortClassName(string $className): string
    {
        if (false !== ($pos = mb_strrpos($className, '\\'))) {
            return mb_substr($className, $pos + 1);
        }

        return $className;
    }
}
