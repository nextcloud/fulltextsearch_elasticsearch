<?php
declare(strict_types=1);


/**
 * FullTextSearch_OpenSearch - Use OpenSearch to index the content of your nextcloud
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2018
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


namespace OCA\FullTextSearch_OpenSearch\Service;


use OCA\FullTextSearch_OpenSearch\AppInfo\Application;
use OCA\FullTextSearch_OpenSearch\Exceptions\ConfigurationException;
use OCP\IConfig;


/**
 * Class ConfigService
 *
 * @package OCA\FullTextSearch_OpenSearch\Service
 */
class ConfigService {

	const FIELDS_LIMIT = 'fields_limit';
	const ELASTIC_HOST = 'elastic_host';
	const ELASTIC_INDEX = 'elastic_index';
	const ELASTIC_VER_BELOW66 = 'es_ver_below66';
	const ELASTIC_LOGGER_ENABLED = 'elastic_logger_enabled';
	const ANALYZER_TOKENIZER = 'analyzer_tokenizer';
	const ALLOW_SELF_SIGNED_CERT = 'allow_self_signed_cert';

	public static array $defaults = [
		self::ELASTIC_HOST => '',
		self::ELASTIC_INDEX => '',
		self::FIELDS_LIMIT => '10000',
		self::ELASTIC_VER_BELOW66 => '0',
		self::ELASTIC_LOGGER_ENABLED => 'true',
		self::ANALYZER_TOKENIZER => 'standard',
		self::ALLOW_SELF_SIGNED_CERT => 'false'
	];

	public function __construct(
		private IConfig $config
	) {
	}


	/**
	 * @return array
	 */
	public function getConfig(): array {
		$keys = array_keys(self::$defaults);
		$data = [];

		foreach ($keys as $k) {
			$data[$k] = $this->getAppValue($k);
		}

		return $data;
	}


	/**
	 * @param array $save
	 */
	public function setConfig(array $save) {
		$keys = array_keys(self::$defaults);

		foreach ($keys as $k) {
			if (array_key_exists($k, $save)) {
				$this->setAppValue($k, $save[$k]);
			}
		}
	}


	/**
	 * @return array
	 * @throws ConfigurationException
	 */
	public function getElasticHost(): array {
		$strHost = $this->getAppValue(self::ELASTIC_HOST);
		if ($strHost === '') {
			throw new ConfigurationException(
				'Your OpenSearchPlatform is not configured properly'
			);
		}

		$hosts = explode(',', $strHost);

		return array_map('trim', $hosts);
	}


	/**
	 * @return string
	 * @throws ConfigurationException
	 */
	public function getElasticIndex(): string {

		$index = $this->getAppValue(self::ELASTIC_INDEX);
		if ($index === '') {
			throw new ConfigurationException(
				'Your OpenSearchPlatform is not configured properly'
			);
		}

		return $index;
	}


	/**
	 * Get a value by key
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function getAppValue(string $key): string {
		$defaultValue = null;
		if (array_key_exists($key, self::$defaults)) {
			$defaultValue = self::$defaults[$key];
		}

		return $this->config->getSystemValueString(
			Application::APP_NAME . '.' . $key,
			(string)$this->config->getAppValue(Application::APP_NAME, $key, $defaultValue)
		);
	}


	public function getAppValueBool(string $key): bool {
		$value = $this->config->getAppValue(Application::APP_NAME, $key, null) ?? self::$defaults[$key] ?? false;
		if (is_bool($value)) {
			return $value;
		}

		if ($value === 1 ||
			$value === '1' ||
			strtolower($value) === 'true' ||
			strtolower($value) === 'yes') {
			return true;
		}

		return false;
	}

	/**
	 * Set a value by key
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function setAppValue(string $key, string $value) {
		$this->config->setAppValue(Application::APP_NAME, $key, $value);
	}

	/**
	 * remove a key
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function deleteAppValue(string $key): string {
		return $this->config->deleteAppValue(Application::APP_NAME, $key);
	}

	/**
	 * TODO: check json sent by admin front-end are valid.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function checkConfig(array $data): bool {
		return true;
	}
}

