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

namespace Fidry\Console\Tests\Command\Fixture;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;
use Fidry\Console\ExitCode;
use Fidry\Console\Helper\QuestionHelper;
use Fidry\Console\IO;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\ProcessHelper;

final class CommandWithHelpers implements Command
{
    public function __construct(
        DebugFormatterHelper $debugFormatterHelper,
        DescriptorHelper $descriptorHelper,
        FormatterHelper $formatterHelper,
        ProcessHelper $processHelper,
        QuestionHelper $questionHelper
    ) {
    }

    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'app:helpers',
            '',
            '',
        );
    }

    public function execute(IO $io): int
    {
        return ExitCode::SUCCESS;
    }
}
