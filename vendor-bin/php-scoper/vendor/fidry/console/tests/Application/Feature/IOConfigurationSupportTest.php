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

namespace Fidry\Console\Tests\Application\Feature;

use Fidry\Console\Application\Application;
use Fidry\Console\Application\ApplicationRunner;
use Fidry\Console\Tests\Application\Fixture\ApplicationWithConfigurableIO;
use Fidry\Console\Tests\Application\Fixture\SimpleApplication;
use Fidry\Console\Tests\StatefulService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers \Fidry\Console\Application\ApplicationRunner
 * @covers \Fidry\Console\Application\SymfonyApplication
 */
final class IOConfigurationSupportTest extends TestCase
{
    public function test_it_supports_application_which_configures_the_io(): void
    {
        $service = new StatefulService();

        // Sanity check
        self::assertFalse($service->called);

        ApplicationRunner::runApplication(
            new ApplicationWithConfigurableIO($service),
            new StringInput('list'),
            new NullOutput(),
        );

        /** @psalm-suppress DocblockTypeContradiction */
        self::assertTrue($service->called);
    }

    /**
     * @dataProvider applicationProvider
     */
    public function test_it_uses_the_original_symfony_configuration(Application $application): void
    {
        unset($_ENV['SHELL_VERBOSITY']);

        ApplicationRunner::runApplication(
            $application,
            new StringInput('list'),
            new NullOutput(),
        );

        self::assertArrayHasKey('SHELL_VERBOSITY', $_ENV);
    }

    public static function applicationProvider(): iterable
    {
        yield 'application with no configured IO' => [
            new SimpleApplication(),
        ];

        yield 'application with configured IO' => [
            new ApplicationWithConfigurableIO(new StatefulService()),
        ];
    }
}
