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

namespace Fidry\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function func_get_args;

/**
 * @property OutputInterface $output
 *
 * @internal
 * @psalm-require-implements OutputInterface
 */
trait DecoratesOutput
{
    public function write(iterable|string $messages, bool $newline = false, int $options = 0): void
    {
        $this->output->write(...func_get_args());
    }

    public function writeln(iterable|string $messages, int $options = 0): void
    {
        $this->output->writeln(...func_get_args());
    }

    public function setVerbosity(int $level): void
    {
        $this->output->setVerbosity(...func_get_args());
    }

    /**
     * @return OutputInterface::VERBOSITY_*
     */
    public function getVerbosity(): int
    {
        return $this->output->getVerbosity(...func_get_args());
    }

    public function isQuiet(): bool
    {
        return $this->output->isQuiet(...func_get_args());
    }

    public function isVerbose(): bool
    {
        return $this->output->isVerbose(...func_get_args());
    }

    public function isVeryVerbose(): bool
    {
        return $this->output->isVeryVerbose(...func_get_args());
    }

    public function isDebug(): bool
    {
        return $this->output->isDebug(...func_get_args());
    }

    public function setDecorated(bool $decorated): void
    {
        $this->output->setDecorated(...func_get_args());
    }

    public function isDecorated(): bool
    {
        return $this->output->isDecorated(...func_get_args());
    }

    public function setFormatter(OutputFormatterInterface $formatter): void
    {
        $this->output->setFormatter(...func_get_args());
    }

    public function getFormatter(): OutputFormatterInterface
    {
        return $this->output->getFormatter(...func_get_args());
    }
}
