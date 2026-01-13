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
use Webmozart\Assert\Assert;

/**
 * @implements InputType<positive-int|0>
 */
final class NaturalRangeType implements InputType
{
    private int $min;
    private int $max;

    public function __construct(int $min, int $max)
    {
        if ($min < $max) {
            $this->min = $min;
            $this->max = $max;
        } else {
            $this->min = $max;
            $this->max = $min;
        }
    }

    public function coerceValue(array|bool|string|null $value, string $label): int
    {
        $intValue = (new NaturalType())->coerceValue($value, $label);

        /** @psalm-suppress MissingClosureReturnType */
        InputAssert::castThrowException(
            fn () => Assert::range(
                $intValue,
                $this->min,
                $this->max,
            ),
            $label,
        );

        return $intValue;
    }

    public function getTypeClassNames(): array
    {
        return [self::class];
    }

    public function getPsalmTypeDeclaration(): string
    {
        return 'positive-int|0';
    }

    public function getPhpTypeDeclaration(): ?string
    {
        return 'int';
    }
}
