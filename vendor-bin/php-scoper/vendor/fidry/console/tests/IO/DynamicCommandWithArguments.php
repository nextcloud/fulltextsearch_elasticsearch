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

namespace Fidry\Console\Tests\IO;

use Fidry\Console\ExitCode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DynamicCommandWithArguments extends Command
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public InputInterface $input;

    public function __construct(InputArgument $inputArgument)
    {
        parent::__construct('app:input:args');

        $this->getDefinition()->addArgument($inputArgument);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;

        return ExitCode::SUCCESS;
    }
}
