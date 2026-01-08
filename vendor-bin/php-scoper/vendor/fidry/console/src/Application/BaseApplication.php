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

namespace Fidry\Console\Application;

use function sprintf;

/**
 * Base class for an application which mimics the behaviour contained by the
 * Symfony Application base class.
 *
 * @see \Symfony\Component\Console\Application
 */
abstract class BaseApplication implements Application
{
    public function getLongVersion(): string
    {
        if ('UNKNOWN' !== $this->getName()) {
            if ('UNKNOWN' !== $this->getVersion()) {
                return sprintf(
                    '%s <info>%s</info>',
                    $this->getName(),
                    $this->getVersion(),
                );
            }

            return $this->getName();
        }

        return 'Console Tool';
    }

    public function getHelp(): string
    {
        return $this->getLongVersion();
    }

    public function getDefaultCommand(): string
    {
        return 'list';
    }

    public function isAutoExitEnabled(): bool
    {
        return true;
    }

    public function areExceptionsCaught(): bool
    {
        return true;
    }
}
