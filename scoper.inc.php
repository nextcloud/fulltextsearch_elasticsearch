<?php

declare(strict_types=1);

/*
 * FullTextSearch_ElasticSearch - Use Elasticsearch to index the content of your nextcloud
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2024
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

use Isolated\Symfony\Component\Finder\Finder;

// based on Arthur Schiwon blogpost:
// https://arthur-schiwon.de/isolating-nextcloud-app-dependencies-php-scoper

return [
	'prefix' => 'OCA\\FullTextSearch_Elasticsearch\\Vendor',
	'exclude-namespaces' => ['Composer'],
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
