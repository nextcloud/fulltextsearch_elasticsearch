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

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Fidry\Console\Input;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use function func_get_args;

/**
 * @property InputInterface $input
 *
 * @internal
 * @psalm-require-implements InputInterface
 */
trait DecoratesInput
{
    public function getArgument(string $name): mixed
    {
        return $this->input->getArgument($name);
    }

    public function getFirstArgument(): ?string
    {
        return $this->input->getFirstArgument();
    }

    public function hasParameterOption(array|string $values, bool $onlyParams = false): bool
    {
        return $this->input->hasParameterOption(...func_get_args());
    }

    public function getParameterOption(
        array|string $values,
        array|bool|float|int|string|null $default = false,
        bool $onlyParams = false
    ): mixed {
        return $this->input->getParameterOption(...func_get_args());
    }

    public function bind(InputDefinition $definition): void
    {
        $this->input->bind($definition);
    }

    public function validate(): void
    {
        $this->input->validate();
    }

    /**
     * @return array<string|bool|int|float|array|null>
     */
    public function getArguments(): array
    {
        return $this->input->getArguments();
    }

    public function setArgument(string $name, mixed $value): void
    {
        $this->input->setArgument(...func_get_args());
    }

    public function hasArgument(string $name): bool
    {
        return $this->input->hasArgument(...func_get_args());
    }

    /**
     * @return array<string|bool|int|float|array|null>
     */
    public function getOptions(): array
    {
        return $this->input->getOptions();
    }

    public function setOption(string $name, mixed $value): void
    {
        $this->input->setOption(...func_get_args());
    }

    public function getOption(string $name): mixed
    {
        return $this->input->getOption(...func_get_args());
    }

    public function hasOption(string $name, bool $onlyRealParams = false): bool
    {
        return $this->input->hasOption(...func_get_args());
    }

    public function isInteractive(): bool
    {
        return $this->input->isInteractive();
    }

    public function setInteractive(bool $interactive): void
    {
        $this->input->setInteractive(...func_get_args());
    }

    public function __toString(): string
    {
        return $this->input->__toString();
    }
}
