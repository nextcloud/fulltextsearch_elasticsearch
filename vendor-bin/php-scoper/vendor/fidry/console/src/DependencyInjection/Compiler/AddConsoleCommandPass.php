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

use Fidry\Console\Bridge\Command\SymfonyCommand;
use Fidry\Console\Command\LazyCommand;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use function sprintf;

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

        /** @psalm-suppress ArgumentTypeCoercion */
        $commandTagAttributes = self::createCommandTagAttributes(
            $id,
            $decoratedCommandDefinition->getClass(),
            $containerBuilder,
        );

        $definition = new Definition(
            SymfonyCommand::class,
            [$decoratedCommandDefinition],
        );
        $definition->addTag('console.command', $commandTagAttributes);

        return $definition;
    }

    /**
     * @param class-string|null $definitionClassName
     *
     * @return array<string, string>
     */
    private static function createCommandTagAttributes(
        string $id,
        ?string $definitionClassName,
        ContainerBuilder $containerBuilder
    ): array {
        if (!self::isLazyCommand($id, $definitionClassName, $containerBuilder)) {
            return [];
        }

        return [
            'command' => $definitionClassName::getName(),
            'description' => $definitionClassName::getDescription(),
        ];
    }

    /**
     * @param class-string|null $definitionClassName
     *
     * @psalm-assert-if-true class-string<LazyCommand> $definitionClassName
     */
    private static function isLazyCommand(
        string $id,
        ?string $definitionClassName,
        ContainerBuilder $containerBuilder
    ): bool {
        if (null === $definitionClassName) {
            return false;
        }

        $classReflection = $containerBuilder->getReflectionClass($definitionClassName);

        if (null === $classReflection) {
            throw new InvalidArgumentException(
                sprintf(
                    'Class "%s" used for service "%s" cannot be found.',
                    $definitionClassName,
                    $id,
                ),
            );
        }

        return $classReflection->isSubclassOf(LazyCommand::class);
    }
}
