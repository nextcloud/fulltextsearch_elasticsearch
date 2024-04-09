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
    private string $name;
    private string $description;
    private string $help;

    /**
     * @var InputArgument[]
     */
    private array $arguments;

    /**
     * @var InputOption[]
     */
    private array $options;

    public function __construct(
        string $name,
        string $description,
        string $help,
        array $arguments = [],
        array $options = []
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->help = $help;
        $this->arguments = $arguments;
        $this->options = $options;
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
     * @var InputArgument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @var InputOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
