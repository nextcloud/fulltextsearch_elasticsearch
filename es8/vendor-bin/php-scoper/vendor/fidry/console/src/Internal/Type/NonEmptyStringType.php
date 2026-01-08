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

use Fidry\Console\Input\InvalidInputValueType;
use Fidry\Console\Internal\InputAssert;
use function mb_trim;
use function sprintf;

/**
 * @implements ScalarType<non-empty-string>
 */
final class NonEmptyStringType implements ScalarType
{
    public function coerceValue(array|bool|string|null $value, string $label): string
    {
        InputAssert::string($value, $label);

        $trimmedValue = mb_trim($value);

        if ('' === $trimmedValue) {
            throw new InvalidInputValueType(
                sprintf(
                    'Expected a non-empty string for %s.',
                    $label,
                ),
            );
        }

        return $trimmedValue;
    }

    public function getTypeClassNames(): array
    {
        return [self::class];
    }

    public function getPsalmTypeDeclaration(): string
    {
        return 'non-empty-string';
    }

    public function getPhpTypeDeclaration(): ?string
    {
        return 'string';
    }
}
