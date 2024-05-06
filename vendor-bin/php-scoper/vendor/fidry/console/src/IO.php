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

namespace Fidry\Console;

use function array_map;
use Fidry\Console\Command\ConsoleAssert;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class IO extends SymfonyStyle
{
    private InputInterface $input;
    private OutputInterface $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);

        $this->input = $input;
        $this->output = $output;
    }

    public static function createNull(): self
    {
        return new self(
            new StringInput(''),
            new NullOutput()
        );
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function getBooleanArgument(string $name): bool
    {
        $argument = $this->getArgument($name);

        ConsoleAssert::assertIsNotArray($argument);

        return (bool) $argument;
    }

    public function getNullableBooleanArgument(string $name): ?bool
    {
        if (null === $this->getArgument($name)) {
            return null;
        }

        return $this->getBooleanArgument($name);
    }

    public function getStringArgument(string $name): string
    {
        $argument = $this->getArgument($name);

        ConsoleAssert::assertIsNotArray($argument);

        return (string) $argument;
    }

    public function getNullableStringArgument(string $name): ?string
    {
        if (null === $this->getArgument($name)) {
            return null;
        }

        return $this->getStringArgument($name);
    }

    /**
     * @return string[]
     */
    public function getStringArrayArgument(string $name): array
    {
        $argument = $this->getArgument($name);

        ConsoleAssert::assertIsArray($argument);

        return $argument;
    }

    public function getIntegerArgument(string $name): int
    {
        $argument = $this->getArgument($name);

        ConsoleAssert::assertIsNotArray($argument);
        ConsoleAssert::integer($argument);

        return (int) $argument;
    }

    public function getNullableIntegerArgument(string $name): ?int
    {
        if (null === $this->getArgument($name)) {
            return null;
        }

        return $this->getIntegerArgument($name);
    }

    /**
     * @return int[]
     */
    public function getIntegerArrayArgument(string $name): array
    {
        $argument = $this->getArgument($name);

        ConsoleAssert::assertIsArray($argument);

        return array_map(
            static function ($element): int {
                ConsoleAssert::integer($element);

                return (int) $element;
            },
            $argument,
        );
    }

    public function getFloatArgument(string $name): float
    {
        $argument = $this->getArgument($name);

        ConsoleAssert::assertIsNotArray($argument);
        ConsoleAssert::numeric($argument);

        return (float) $argument;
    }

    public function getNullableFloatArgument(string $name): ?float
    {
        if (null === $this->getArgument($name)) {
            return null;
        }

        return $this->getFloatArgument($name);
    }

    /**
     * @return float[]
     */
    public function getFloatArrayArgument(string $name): array
    {
        $argument = $this->getArgument($name);

        ConsoleAssert::assertIsArray($argument);

        return array_map(
            static function ($element): float {
                ConsoleAssert::numeric($element);

                return (float) $element;
            },
            $argument,
        );
    }

    public function getBooleanOption(string $name): bool
    {
        $option = $this->getOption($name);

        ConsoleAssert::assertIsNotArray($option);

        return (bool) $option;
    }

    public function getNullableBooleanOption(string $name): ?bool
    {
        if (null === $this->getOption($name)) {
            return null;
        }

        return $this->getBooleanOption($name);
    }

    public function getStringOption(string $name): string
    {
        $option = $this->getOption($name);

        ConsoleAssert::assertIsNotArray($option);

        return (string) $option;
    }

    public function getNullableStringOption(string $name): ?string
    {
        if (null === $this->getOption($name)) {
            return null;
        }

        return $this->getStringOption($name);
    }

    /**
     * @return string[]
     */
    public function getStringArrayOption(string $name): array
    {
        $option = $this->getOption($name);

        ConsoleAssert::assertIsArray($option);

        return $option;
    }

    public function getIntegerOption(string $name): int
    {
        $option = $this->getOption($name);

        ConsoleAssert::assertIsNotArray($option);
        ConsoleAssert::integer($option);

        return (int) $option;
    }

    public function getNullableIntegerOption(string $name): ?int
    {
        if (null === $this->getOption($name)) {
            return null;
        }

        return $this->getIntegerOption($name);
    }

    /**
     * @return int[]
     */
    public function getIntegerArrayOption(string $name): array
    {
        $option = $this->getOption($name);

        ConsoleAssert::assertIsArray($option);

        return array_map(
            static function ($element): int {
                ConsoleAssert::integer($element);

                return (int) $element;
            },
            $option,
        );
    }

    public function getFloatOption(string $name): float
    {
        $option = $this->getOption($name);

        ConsoleAssert::assertIsNotArray($option);
        ConsoleAssert::numeric($option);

        return (float) $option;
    }

    public function getNullableFloatOption(string $name): ?float
    {
        if (null === $this->getOption($name)) {
            return null;
        }

        return $this->getFloatOption($name);
    }

    /**
     * @return float[]
     */
    public function getFloatArrayOption(string $name): array
    {
        $option = $this->getOption($name);

        ConsoleAssert::assertIsArray($option);

        return array_map(
            static function ($element): float {
                ConsoleAssert::numeric($element);

                return (float) $element;
            },
            $option,
        );
    }

    public function isInteractive(): bool
    {
        return $this->input->isInteractive();
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @return null|string|string[]
     */
    private function getArgument(string $name)
    {
        $argument = $this->input->getArgument($name);

        ConsoleAssert::assertIsValidArgumentType($argument);

        return $argument;
    }

    /**
     * @return null|bool|string|string[]
     */
    private function getOption(string $name)
    {
        $option = $this->input->getOption($name);

        ConsoleAssert::assertIsValidOptionType($option);

        return $option;
    }
}
