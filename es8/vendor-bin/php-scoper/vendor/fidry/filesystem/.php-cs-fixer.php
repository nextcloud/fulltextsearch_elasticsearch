<?php declare(strict_types=1);

/*
 * This file is part of the fidry/filesystem package.
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
    ]);

$header = trim(
    sprintf(
    'This code is licensed under the BSD 3-Clause License.%s',
    substr(
        file_get_contents('LICENSE'),
        strlen('BSD 3-Clause License') + 1,
    ),
));

$config = new FidryConfig($header, 81_000);
$config->setCacheFile(__DIR__.'/dist/.php-cs-fixer.cache');

return $config->setFinder($finder);
