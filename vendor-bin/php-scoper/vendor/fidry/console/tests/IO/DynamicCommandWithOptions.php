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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class DynamicCommandWithOptions extends Command
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public InputInterface $input;

    public function __construct(InputOption $inputOption)
    {
        parent::__construct('app:input:opts');

        $this->getDefinition()->addOption($inputOption);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;

        return ExitCode::SUCCESS;
    }
}
