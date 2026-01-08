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
 * @template CastedValueType
 * @implements InputType<CastedValueType|null>
 */
final class NullableType implements InputType
{
    /**
     * @var InputType<CastedValueType>
     */
    private InputType $innerType;

    /**
     * @param InputType<CastedValueType> $innerType
     */
    public function __construct(InputType $innerType)
    {
        $this->innerType = $innerType;
    }

    public function coerceValue(array|bool|string|null $value, string $label): mixed
    {
        return null === $value
            ? $value
            : $this->innerType->coerceValue($value, $label);
    }

    public function getTypeClassNames(): array
    {
        return [
            self::class,
            ...$this->innerType->getTypeClassNames(),
        ];
    }

    public function getPsalmTypeDeclaration(): string
    {
        return 'null|'.$this->innerType->getPsalmTypeDeclaration();
    }

    public function getPhpTypeDeclaration(): ?string
    {
        $innerPhpTypeDeclaration = $this->innerType->getPhpTypeDeclaration();

        return null === $innerPhpTypeDeclaration ? null : '?'.$innerPhpTypeDeclaration;
    }
}
