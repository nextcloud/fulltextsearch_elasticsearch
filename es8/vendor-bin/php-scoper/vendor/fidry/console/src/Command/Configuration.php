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

namespace Fidry\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Represents the command configuration.
 */
final class Configuration
{
    /**
     * Beware that if the command is lazy, the name and description will be
     * overwritten by the values provided for the laziness (see the LazyCommand
     * API).
     *
     * @param InputArgument[] $arguments
     * @param InputOption[]   $options
     */
    public function __construct(
        private readonly string $name,
        private readonly string $description,
        private readonly string $help,
        private readonly array $arguments = [],
        private readonly array $options = [],
        private readonly bool $hidden = false,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getHelp(): string
    {
        return $this->help;
    }

    /**
     * @return InputArgument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return InputOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }
}
