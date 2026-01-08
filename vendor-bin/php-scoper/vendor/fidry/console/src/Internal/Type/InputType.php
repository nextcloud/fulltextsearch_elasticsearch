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
 * @psalm-import-type ArgumentInput from \Fidry\Console\Internal\InputAssert
 * @psalm-import-type OptionInput from \Fidry\Console\Internal\InputAssert
 * @template TypedValue
 */
interface InputType
{
    /**
     * @param ArgumentInput|OptionInput $value Valid argument or option value
     * @param non-empty-string          $label
     *
     * @return TypedValue
     */
    public function coerceValue(array|bool|string|null $value, string $label): mixed;

    /**
     * @return non-empty-list<class-string<InputType>>
     */
    public function getTypeClassNames(): array;

    /**
     * @return non-empty-string
     */
    public function getPsalmTypeDeclaration(): string;

    /**
     * @return non-empty-string|null
     */
    public function getPhpTypeDeclaration(): ?string;
}
