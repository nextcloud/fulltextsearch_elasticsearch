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

/**
 * @implements ScalarType<non-empty-string|null>
 */
final class NullOrNonEmptyStringType implements ScalarType
{
    public function coerceValue(array|bool|string|null $value, string $label): ?string
    {
        $trimmedValue = (new StringType())->coerceValue($value, $label);

        return '' === $trimmedValue ? null : $trimmedValue;
    }

    public function getTypeClassNames(): array
    {
        return [self::class];
    }

    public function getPsalmTypeDeclaration(): string
    {
        return 'null|non-empty-string';
    }

    public function getPhpTypeDeclaration(): ?string
    {
        return '?string';
    }
}
