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
use function array_map;
use function sprintf;

/**
 * @template TypedValue
 * @implements InputType<list<TypedValue>>
 */
final class ListType implements InputType
{
    /**
     * @var InputType<TypedValue>
     */
    private InputType $innerType;

    /**
     * @param InputType<TypedValue> $innerType
     */
    public function __construct(InputType $innerType)
    {
        $this->innerType = $innerType;
    }

    public function coerceValue(array|bool|string|null $value, string $label): array
    {
        InputAssert::assertIsList($value, $label);

        return array_map(
            fn (string $element) => $this->innerType->coerceValue($element, $label),
            $value,
        );
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
        return sprintf(
            'list<%s>',
            $this->innerType->getPsalmTypeDeclaration(),
        );
    }

    public function getPhpTypeDeclaration(): ?string
    {
        return 'array';
    }
}
