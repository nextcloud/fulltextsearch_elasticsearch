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

namespace Fidry\Console\Input;

use Symfony\Component\Console\Exception\InvalidArgumentException as ConsoleInvalidArgumentException;
use Webmozart\Assert\InvalidArgumentException as AssertInvalidArgumentException;
use function sprintf;

final class InvalidInputValueType extends ConsoleInvalidArgumentException
{
    /**
     * @param non-empty-string $inputLabel
     */
    public static function fromAssert(
        AssertInvalidArgumentException $exception,
        string $inputLabel
    ): self {
        return new self(
            sprintf(
                '%s for %s.',
                $exception->getMessage(),
                $inputLabel,
            ),
            $exception->getCode(),
            $exception,
        );
    }

    public static function withErrorMessage(
        self $exception,
        string $errorMessage
    ): self {
        return new self(
            sprintf(
                $errorMessage,
                $exception->getMessage(),
            ),
            $exception->getCode(),
            $exception,
        );
    }
}
