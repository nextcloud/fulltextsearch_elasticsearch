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

use Fidry\Console\Internal\InputAssert;

// TODO: to split into safe & unsafe
/**
 * @implements ScalarType<bool>
 */
final class BooleanType implements ScalarType
{
    public function coerceValue(array|bool|string|null $value, string $label): bool
    {
        InputAssert::assertIsScalar($value, $label);

        return (bool) $value;
    }

    public function getTypeClassNames(): array
    {
        return [self::class];
    }

    public function getPsalmTypeDeclaration(): string
    {
        return 'bool';
    }

    public function getPhpTypeDeclaration(): ?string
    {
        return 'bool';
    }
}
