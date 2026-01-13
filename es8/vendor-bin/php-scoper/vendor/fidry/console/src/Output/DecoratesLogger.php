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

use Psr\Log\LoggerInterface;
use Stringable;
use function func_get_args;

/**
 * @internal
 */
trait DecoratesLogger
{
    private LoggerInterface $logger;

    public function logEmergency(string|Stringable $message, array $context = []): void
    {
        $this->logger->emergency(...func_get_args());
    }

    public function logAlert(string|Stringable $message, array $context = []): void
    {
        $this->logger->alert(...func_get_args());
    }

    public function logCritical(string|Stringable $message, array $context = []): void
    {
        $this->logger->critical(...func_get_args());
    }

    public function logError(string|Stringable $message, array $context = []): void
    {
        $this->logger->error(...func_get_args());
    }

    public function logWarning(string|Stringable $message, array $context = []): void
    {
        $this->logger->warning(...func_get_args());
    }

    public function logNotice(string|Stringable $message, array $context = []): void
    {
        $this->logger->notice(...func_get_args());
    }

    public function logInfo(string|Stringable $message, array $context = []): void
    {
        $this->logger->info(...func_get_args());
    }

    public function logDebug(string|Stringable $message, array $context = []): void
    {
        $this->logger->debug(...func_get_args());
    }

    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $this->logger->log(...func_get_args());
    }
}
