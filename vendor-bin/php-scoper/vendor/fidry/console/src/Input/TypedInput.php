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

namespace Fidry\Console\Input;

use BackedEnum;
use Fidry\Console\Internal\InputAssert;
use Fidry\Console\Internal\Type\BackedEnumType;
use Fidry\Console\Internal\Type\NaturalRangeType;
use Fidry\Console\Internal\Type\NullableType;
use Fidry\Console\Internal\Type\StringChoiceType;
use Fidry\Console\Internal\Type\TypeFactory;
use Webmozart\Assert\Assert;
use function sprintf;

/**
 * @psalm-import-type ArgumentInput from \Fidry\Console\Internal\InputAssert
 * @psalm-import-type OptionInput from \Fidry\Console\Internal\InputAssert
 */
final class TypedInput
{
    /**
     * @param ArgumentInput|OptionInput $value
     * @param non-empty-string          $label
     */
    private function __construct(
        private readonly array|bool|string|null $value,
        private readonly string $label,
    ) {
        Assert::stringNotEmpty($label);
    }

    /**
     * @param non-empty-string $name
     *
     * @psalm-assert ArgumentInput $argument
     */
    public static function fromArgument(mixed $argument, string $name): self
    {
        InputAssert::assertIsValidArgumentType($argument, $name);

        return new self(
            $argument,
            sprintf(
                'the argument "%s"',
                $name,
            ),
        );
    }

    /**
     * @param non-empty-string $name
     *
     * @psalm-assert OptionInput $option
     */
    public static function fromOption(mixed $option, string $name): self
    {
        InputAssert::assertIsValidOptionType($option, $name);

        return new self(
            $option,
            sprintf(
                'the option "%s"',
                $name,
            ),
        );
    }

    /**
     * @param list<string> $choices
     */
    public function asStringChoice(
        array $choices,
        ?string $errorMessage = null
    ): string {
        $type = new StringChoiceType($choices);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @param positive-int|0 $min
     * @param positive-int|0 $max
     *
     * @return positive-int|0
     */
    public function asNaturalWithinRange(
        int $min,
        int $max,
        ?string $errorMessage = null
    ): int {
        $type = new NaturalRangeType($min, $max);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @psalm-suppress InvalidReturnType, NoValue
     * @template T of BackedEnum
     *
     * @param class-string<T> $backedEnumClassName
     *
     * @return T
     */
    public function asBackedEnum(
        string $backedEnumClassName,
        ?string $errorMessage = null
    ): BackedEnum {
        $type = new BackedEnumType($backedEnumClassName);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @template T of BackedEnum
     *
     * @param class-string<T> $backedEnumClassName
     *
     * @return T|null
     */
    public function asNullableBackedEnum(
        string $backedEnumClassName,
        ?string $errorMessage = null
    ): ?BackedEnum {
        $type = new NullableType(
            new BackedEnumType($backedEnumClassName),
        );

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    // The following part is auto-generated.
    // __AUTO_GENERATE_START__
    /**
     * @return null|bool|string|list<string>
     */
    public function asRaw(?string $errorMessage = null): array|bool|string|null
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\RawType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    public function asBoolean(?string $errorMessage = null): bool
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\BooleanType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    public function asNullableBoolean(?string $errorMessage = null): ?bool
    {
        $type = TypeFactory::createTypeFromClassNames([
            NullableType::class,
            \Fidry\Console\Internal\Type\BooleanType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return list<bool>
     */
    public function asBooleanList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\ListType::class,
            \Fidry\Console\Internal\Type\BooleanType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return non-empty-list<bool>
     */
    public function asBooleanNonEmptyList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NonEmptyListType::class,
            \Fidry\Console\Internal\Type\BooleanType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return positive-int|0
     */
    public function asNatural(?string $errorMessage = null): int
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NaturalType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return null|positive-int|0
     */
    public function asNullableNatural(?string $errorMessage = null): ?int
    {
        $type = TypeFactory::createTypeFromClassNames([
            NullableType::class,
            \Fidry\Console\Internal\Type\NaturalType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return list<positive-int|0>
     */
    public function asNaturalList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\ListType::class,
            \Fidry\Console\Internal\Type\NaturalType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return non-empty-list<positive-int|0>
     */
    public function asNaturalNonEmptyList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NonEmptyListType::class,
            \Fidry\Console\Internal\Type\NaturalType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return positive-int
     */
    public function asPositiveInteger(?string $errorMessage = null): int
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\PositiveIntegerType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return null|positive-int
     */
    public function asNullablePositiveInteger(?string $errorMessage = null): ?int
    {
        $type = TypeFactory::createTypeFromClassNames([
            NullableType::class,
            \Fidry\Console\Internal\Type\PositiveIntegerType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return list<positive-int>
     */
    public function asPositiveIntegerList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\ListType::class,
            \Fidry\Console\Internal\Type\PositiveIntegerType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return non-empty-list<positive-int>
     */
    public function asPositiveIntegerNonEmptyList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NonEmptyListType::class,
            \Fidry\Console\Internal\Type\PositiveIntegerType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    public function asFloat(?string $errorMessage = null): float
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\FloatType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    public function asNullableFloat(?string $errorMessage = null): ?float
    {
        $type = TypeFactory::createTypeFromClassNames([
            NullableType::class,
            \Fidry\Console\Internal\Type\FloatType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return list<float>
     */
    public function asFloatList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\ListType::class,
            \Fidry\Console\Internal\Type\FloatType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return non-empty-list<float>
     */
    public function asFloatNonEmptyList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NonEmptyListType::class,
            \Fidry\Console\Internal\Type\FloatType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    public function asString(?string $errorMessage = null): string
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\StringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    public function asNullableString(?string $errorMessage = null): ?string
    {
        $type = TypeFactory::createTypeFromClassNames([
            NullableType::class,
            \Fidry\Console\Internal\Type\StringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return list<string>
     */
    public function asStringList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\ListType::class,
            \Fidry\Console\Internal\Type\StringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return non-empty-list<string>
     */
    public function asStringNonEmptyList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NonEmptyListType::class,
            \Fidry\Console\Internal\Type\StringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return non-empty-string
     */
    public function asNonEmptyString(?string $errorMessage = null): string
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NonEmptyStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return null|non-empty-string
     */
    public function asNullableNonEmptyString(?string $errorMessage = null): ?string
    {
        $type = TypeFactory::createTypeFromClassNames([
            NullableType::class,
            \Fidry\Console\Internal\Type\NonEmptyStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return list<non-empty-string>
     */
    public function asNonEmptyStringList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\ListType::class,
            \Fidry\Console\Internal\Type\NonEmptyStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    public function asNonEmptyStringNonEmptyList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NonEmptyListType::class,
            \Fidry\Console\Internal\Type\NonEmptyStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    public function asUntrimmedString(?string $errorMessage = null): string
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\UntrimmedStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    public function asNullableUntrimmedString(?string $errorMessage = null): ?string
    {
        $type = TypeFactory::createTypeFromClassNames([
            NullableType::class,
            \Fidry\Console\Internal\Type\UntrimmedStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return list<string>
     */
    public function asUntrimmedStringList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\ListType::class,
            \Fidry\Console\Internal\Type\UntrimmedStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return non-empty-list<string>
     */
    public function asUntrimmedStringNonEmptyList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NonEmptyListType::class,
            \Fidry\Console\Internal\Type\UntrimmedStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return null|non-empty-string
     */
    public function asNullOrNonEmptyString(?string $errorMessage = null): ?string
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NullOrNonEmptyStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return list<null|non-empty-string>
     */
    public function asNullOrNonEmptyStringList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\ListType::class,
            \Fidry\Console\Internal\Type\NullOrNonEmptyStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }

    /**
     * @return non-empty-list<null|non-empty-string>
     */
    public function asNullOrNonEmptyStringNonEmptyList(?string $errorMessage = null): array
    {
        $type = TypeFactory::createTypeFromClassNames([
            \Fidry\Console\Internal\Type\NonEmptyListType::class,
            \Fidry\Console\Internal\Type\NullOrNonEmptyStringType::class,
        ]);

        if (null === $errorMessage) {
            return $type->coerceValue($this->value, $this->label);
        }

        try {
            return $type->coerceValue($this->value, $this->label);
        } catch (InvalidInputValueType $coercingFailed) {
            throw InvalidInputValueType::withErrorMessage(
                $coercingFailed,
                $errorMessage,
            );
        }
    }
    // __AUTO_GENERATE_END__
}
