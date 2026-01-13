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

namespace Fidry\Console\Output;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\StyleInterface;

/**
 * Complements the Symfony Style interface with the methods present in
 * SymfonyStyle that are not in the interface due to BC breaks concerns.
 *
 * @internal
 */
interface StyledOutput extends StyleInterface
{
    /**
     * Formats a message as a block of text.
     *
     * @return void
     */
    public function block(
        array|string $messages,
        ?string $type = null,
        ?string $style = null,
        string $prefix = ' ',
        bool $padding = false,
        bool $escape = true
    );

    /**
     * Formats a command comment.
     *
     * @return void
     */
    public function comment(array|string $message);

    /**
     * Formats an info message.
     *
     * @return void
     */
    public function info(array|string $message);

    /**
     * Formats a horizontal table.
     *
     * @return void
     */
    public function horizontalTable(array $headers, array $rows);

    /**
     * Formats a list of key/value horizontally.
     *
     * Each row can be one of:
     * * 'A title'
     * * ['key' => 'value']
     * * new TableSeparator()
     *
     * @return void
     */
    public function definitionList(array|string|TableSeparator ...$list);

    /**
     * @see ProgressBar::iterate()
     */
    public function progressIterate(iterable $iterable, ?int $max = null): iterable;

    public function askQuestion(Question $question): mixed;

    public function createTable(): Table;

    public function createProgressBar(int $max = 0): ProgressBar;
}
