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
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use function file_exists;

final class FidryConsoleExtension extends Extension
{
    private const SERVICES_DIR = __DIR__.'/../../resources/config';
    private const HELPER_PATHS = [
        __DIR__.'/../../vendor/symfony/console/Helper',
        __DIR__.'/../../../../../vendor/symfony/console/Helper',
    ];

    public function load(array $configs, ContainerBuilder $container): void
    {
        foreach (self::HELPER_PATHS as $helperPath) {
            if (file_exists($helperPath)) {
                $container->setParameter(
                    'fidry_console_symfony_console_helper_directory',
                    $helperPath,
                );
            }
        }

        $loader = new XmlFileLoader($container, new FileLocator(self::SERVICES_DIR));
        $loader->load('services.xml');

        $container
            ->registerForAutoconfiguration(Command::class)
            ->addTag('fidry.console_command');
    }
}
