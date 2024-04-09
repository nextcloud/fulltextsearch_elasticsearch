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

namespace Fidry\Console\DependencyInjection\Compiler;

use Fidry\Console\Command\LazyCommand;
use Fidry\Console\Command\SymfonyCommand;
use InvalidArgumentException;
use function Safe\sprintf;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Looks for all the console commands registered and registers them as regular
 * Symfony commands. This allows to provide the API at minimal cost since it
 * leverages all the FrameworkBundle work already done.
 */
final class AddConsoleCommandPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->addDefinitions(
            self::createDefinitions($container),
        );
    }

    /**
     * @return array<string, Definition>
     */
    private static function createDefinitions(ContainerBuilder $containerBuilder): array
    {
        $tagsByServiceId = $containerBuilder->findTaggedServiceIds('fidry.console_command');

        $commandDefinitions = [];

        foreach ($tagsByServiceId as $id => $_tags) {
            $commandDefinitions[$id] = self::createDefinition($id, $containerBuilder);
        }

        return $commandDefinitions;
    }

    private static function createDefinition(
        string $id,
        ContainerBuilder $containerBuilder
    ): Definition {
        $decoratedCommandDefinition = $containerBuilder->getDefinition($id);

        $commandName = self::getCommandName(
            $id,
            $decoratedCommandDefinition,
            $containerBuilder,
        );

        $definition = new Definition(
            SymfonyCommand::class,
            [$decoratedCommandDefinition],
        );

        $definition->setTags(
            [
                'console.command' => null !== $commandName
                    ? [['command' => $commandName]]
                    : [],
            ]
        );

        return $definition;
    }

    private static function getCommandName(
        string $id,
        Definition $definition,
        ContainerBuilder $containerBuilder
    ): ?string {
        $className = $definition->getClass();

        if (null === $className) {
            return null;
        }

        $classReflection = $containerBuilder->getReflectionClass($className);

        if (null === $classReflection) {
            throw new InvalidArgumentException(
                sprintf(
                    'Class "%s" used for service "%s" cannot be found.',
                    $className,
                    $id,
                ),
            );
        }

        if ($classReflection->isSubclassOf(LazyCommand::class)) {
            /** @var class-string<LazyCommand> $className */
            return $className::getName();
        }

        return null;
    }
}
