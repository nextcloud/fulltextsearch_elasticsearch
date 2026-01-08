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

use BackedEnum;
use Fidry\Console\Input\InvalidInputValueType;
use Fidry\Console\Internal\InputAssert;
use TypeError;
use ValueError;
use function sprintf;

/**
 * @template T of BackedEnum
 * @implements InputType<T>
 */
final class BackedEnumType implements InputType
{
    /**
     * @param class-string<T> $backedEnumClassName
     */
    public function __construct(
        private readonly string $backedEnumClassName,
    ) {
    }

    public function coerceValue(array|bool|string|null $value, string $label): BackedEnum
    {
        InputAssert::assertIsScalar($value, $label);

        try {
            return $this->coerce($value);
        } catch (ValueError) {
            throw new InvalidInputValueType(
                sprintf(
                    'Expected a value "%s" enum. Got "%s" for %s.',
                    $this->backedEnumClassName,
                    InputAssert::castType($value),
                    $label,
                ),
            );
        }
    }

    public function getTypeClassNames(): array
    {
        return [self::class];
    }

    public function getPsalmTypeDeclaration(): string
    {
        return $this->backedEnumClassName;
    }

    public function getPhpTypeDeclaration(): ?string
    {
        return $this->backedEnumClassName;
    }

    /**
     * @return T
     */
    private function coerce(bool|string|null $value): BackedEnum
    {
        try {
            return $this->backedEnumClassName::from((int) $value);
        } catch (TypeError) {
            /** @psalm-suppress PossiblyInvalidArgument */
            return $this->backedEnumClassName::from($value);
        }
    }
}
