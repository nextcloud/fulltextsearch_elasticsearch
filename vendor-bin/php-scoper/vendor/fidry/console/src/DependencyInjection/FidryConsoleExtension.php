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

namespace Fidry\Console\DependencyInjection;

use Fidry\Console\Command\Command;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class FidryConsoleExtension extends Extension
{
    private const SERVICES_DIR = __DIR__.'/../../resources/config';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(self::SERVICES_DIR));
        $loader->load('services.xml');

        $container
            ->registerForAutoconfiguration(Command::class)
            ->addTag('fidry.console_command');
    }
}
