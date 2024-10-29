<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

use Isolated\Symfony\Component\Finder\Finder;

// based on Arthur Schiwon blogpost:
// https://arthur-schiwon.de/isolating-nextcloud-app-dependencies-php-scoper

return [
	'prefix' => 'OCA\\FullTextSearch_Elasticsearch\\Vendor',
	'exclude-namespaces' => ['Composer', 'Psr\Log'],
	'finders' => [
		Finder::create()->files()
			  ->exclude([
							'test',
							'composer',
							'bin',
						])
			  ->notName('autoload.php')
			  ->in('vendor/elasticsearch')
			  ->in('vendor/elastic')
			  ->in('vendor/guzzlehttp')
			  ->in('vendor/php-http')
			  ->in('vendor/psr'),
//		Finder::create()->files()
//			  ->name('InstalledVersions.php')
//			  ->in('vendor/composer')
	],
];
