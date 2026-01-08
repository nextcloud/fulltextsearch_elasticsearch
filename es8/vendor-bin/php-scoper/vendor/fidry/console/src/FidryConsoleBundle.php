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

namespace Fidry\Console;

use Fidry\Console\DependencyInjection\Compiler\AddConsoleCommandPass;
use Fidry\Console\DependencyInjection\FidryConsoleExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class FidryConsoleBundle extends Bundle
{
    public function getContainerExtension(): Extension
    {
        return new FidryConsoleExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new AddConsoleCommandPass(),
            PassConfig::TYPE_BEFORE_REMOVING,
            // Priority must be higher than Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass in FrameworkBundle
            10,
        );
    }
}
