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

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Question\Question;
use function func_get_args;

/**
 * @internal
 * @psalm-require-implements StyledOutput
 */
trait DecoratesStyledOutput
{
    private StyledOutput $styledOutput;

    public function block(array|string $messages, ?string $type = null, ?string $style = null, string $prefix = ' ', bool $padding = false, bool $escape = true): void
    {
        $this->styledOutput->block(...func_get_args());
    }

    public function title(string $message): void
    {
        $this->styledOutput->title(...func_get_args());
    }

    public function section(string $message): void
    {
        $this->styledOutput->section(...func_get_args());
    }

    public function listing(array $elements): void
    {
        $this->styledOutput->listing(...func_get_args());
    }

    public function text(array|string $message): void
    {
        $this->styledOutput->text(...func_get_args());
    }

    public function comment(array|string $message): void
    {
        $this->styledOutput->comment(...func_get_args());
    }

    public function success(array|string $message): void
    {
        $this->styledOutput->success(...func_get_args());
    }

    public function error(array|string $message): void
    {
        $this->styledOutput->error(...func_get_args());
    }

    public function warning(array|string $message): void
    {
        $this->styledOutput->warning(...func_get_args());
    }

    public function note(array|string $message): void
    {
        $this->styledOutput->note(...func_get_args());
    }

    public function info(array|string $message): void
    {
        $this->styledOutput->info(...func_get_args());
    }

    public function caution(array|string $message): void
    {
        $this->styledOutput->caution(...func_get_args());
    }

    public function table(array $headers, array $rows): void
    {
        $this->styledOutput->table(...func_get_args());
    }

    public function horizontalTable(array $headers, array $rows): void
    {
        $this->styledOutput->horizontalTable(...func_get_args());
    }

    public function definitionList(array|string|TableSeparator ...$list): void
    {
        $this->styledOutput->definitionList(...func_get_args());
    }

    public function ask(string $question, ?string $default = null, ?callable $validator = null): mixed
    {
        return $this->styledOutput->ask(...func_get_args());
    }

    public function askHidden(string $question, ?callable $validator = null): mixed
    {
        return $this->styledOutput->askHidden(...func_get_args());
    }

    public function confirm(string $question, bool $default = true): bool
    {
        return $this->styledOutput->confirm(...func_get_args());
    }

    public function choice(string $question, array $choices, mixed $default = null, bool $multiSelect = false): mixed
    {
        return $this->styledOutput->choice(...func_get_args());
    }

    public function progressStart(int $max = 0): void
    {
        $this->styledOutput->progressStart(...func_get_args());
    }

    public function progressAdvance(int $step = 1): void
    {
        $this->styledOutput->progressAdvance(...func_get_args());
    }

    public function progressFinish(): void
    {
        $this->styledOutput->progressFinish(...func_get_args());
    }

    public function createProgressBar(int $max = 0): ProgressBar
    {
        return $this->styledOutput->createProgressBar(...func_get_args());
    }

    public function progressIterate(iterable $iterable, ?int $max = null): iterable
    {
        return $this->styledOutput->progressIterate(...func_get_args());
    }

    public function askQuestion(Question $question): mixed
    {
        return $this->styledOutput->askQuestion(...func_get_args());
    }

    public function newLine(int $count = 1): void
    {
        $this->styledOutput->newLine(...func_get_args());
    }

    public function createTable(): Table
    {
        return $this->styledOutput->createTable(...func_get_args());
    }
}
