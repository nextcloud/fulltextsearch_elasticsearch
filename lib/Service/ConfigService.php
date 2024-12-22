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

	public const FIELDS_LIMIT = 'fields_limit';
	public const OPENSEARCH_HOST = 'opensearch_host';
	public const OPENSEARCH_INDEX = 'opensearch_index';
	public const OPENSEARCH_LOGGER_ENABLED = 'opensearch_logger_enabled';
	public const ANALYZER_TOKENIZER = 'analyzer_tokenizer';
	public const ALLOW_SELF_SIGNED_CERT = 'allow_self_signed_cert';

	public static array $defaults = [
		self::OPENSEARCH_HOST => '',
		self::OPENSEARCH_INDEX => '',
		self::FIELDS_LIMIT => '10000',
		self::OPENSEARCH_LOGGER_ENABLED => 'true',
		self::ANALYZER_TOKENIZER => 'standard',
		self::ALLOW_SELF_SIGNED_CERT => 'false'
	];

	public function __construct(
		private IConfig $config,
	) {
	}


    /**
     * @return array
     */
	final public function getConfig(): array {
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
	final public function setConfig(array $save): void
    {
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
	final public function getOpenSearchHost(): array {
		$strHost = $this->getAppValue(self::OPENSEARCH_HOST);
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
	final public function getOpenSearchIndex(): string {

		$index = $this->getAppValue(self::OPENSEARCH_INDEX);
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
	final public function getAppValue(string $key): string {
        $defaultValue = self::$defaults[$key] ?? '';

		return $this->config->getSystemValueString(
			Application::APP_NAME . '.' . $key,
			(string)$this->config->getAppValue(Application::APP_NAME, $key, $defaultValue)
		);
	}


	final public function getAppValueBool(string $key): bool {
        $defaultValue = self::$defaults[$key] ?? '';
        $value = $this->config->getAppValue(Application::APP_NAME, $key, $defaultValue);
        if($value && is_string($value)){
            if (in_array(strtolower($value), ['1', 'true', 'yes'], true)) {
                return true;
            }
        }

		return false;
	}

	/**
	 * Set a value by key
	 *
	 * @param string $key
	 * @param string $value
	 */
	final public function setAppValue(string $key, string $value): void
    {
		$this->config->setAppValue(Application::APP_NAME, $key, $value);
	}

	/**
	 * remove a key
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	final public function deleteAppValue(string $key): string {
		return $this->config->deleteAppValue(Application::APP_NAME, $key);
	}

    /**
     * Validates the provided configuration data and identifies any errors.
     *
     * @param array $data The configuration data to validate.
     *
     * @return array An array of errors indicating misconfigured keys, or an empty array if no issues are found.
     */
    final public function checkConfig(array $data): array
    {
        $errors = [];

        if (!$this->isValidHost($data[self::OPENSEARCH_HOST] ?? null)) {
            $errors[] = self::OPENSEARCH_HOST;
        }

        if (!$this->isValidIndex($data[self::OPENSEARCH_INDEX] ?? null)) {
            $errors[] = self::OPENSEARCH_INDEX;
        }

        return $errors;
    }

    /**
     * Validates if the provided host is a valid URL with an accepted scheme.
     *
     * @param string|null $host The host to validate. Can be null or a string.
     * @return bool True if the host is valid, false otherwise.
     */
    private function isValidHost(?string $host): bool
    {
        if ($host === null) {
            return false;
        }

        return filter_var($host, FILTER_VALIDATE_URL)
            && in_array(parse_url($host, PHP_URL_SCHEME), ['http', 'https', ''], true);
    }

    /**
     * Validates whether the provided index is a valid string format for an index name.
     *
     * @param string|null $index The index name to validate. Can be null.
     * @return bool Returns true if the index name is valid, false otherwise.
     */
    private function isValidIndex(?string $index): bool
    {
        if ($index === null) {
            return false;
        }

        return preg_match('/^(?![-_])[a-z0-9-_]{1,255}(?<![-_])$/', $index) === 1;
    }
}
