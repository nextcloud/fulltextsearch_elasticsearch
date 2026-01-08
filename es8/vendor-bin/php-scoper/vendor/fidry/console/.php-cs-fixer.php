<?php declare(strict_types=1);

/*
 * This file is part of the Fidry\Console package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Fidry\PhpCsFixerConfig\FidryConfig;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude([
        'dist',
        'tests/Integration/var/cache/',
        'tests/Integration/var/logs/',
    ]);

$config = new FidryConfig(
    <<<'EOF'
        This file is part of the Fidry\Console package.

        (c) Théo FIDRY <theo.fidry@gmail.com>
        
        For the full copyright and license information, please view the LICENSE
        file that was distributed with this source code.
        EOF,
    74_000,
);

$config->addRules([
    'php_unit_internal_class' => false,

    'phpdoc_no_empty_return' => false,
    'void_return' => false,
]);

$config->setFinder($finder);
$config->setCacheFile(__DIR__.'/dist/.php-cs-fixer.cache');

return $config;
