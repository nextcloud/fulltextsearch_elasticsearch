<?php
/**
 * FullNextSearch_ElasticSearch - Index with ElasticSearch
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2017
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

namespace OCA\FullNextSearch_ElasticSearch\Service;

use OCA\FullNextSearch_ElasticSearch\AppInfo\Application;
use OCA\FullNextSearch_ElasticSearch\Exceptions\ConfigurationException;
use OCP\IConfig;
use OCP\PreConditionNotMetException;
use OCP\Util;

class ConfigService {

	const ELASTIC_HOST = 'elastic_host';
	const ELASTIC_INDEX = 'elastic_index';

	private $defaults = [
		self::ELASTIC_HOST  => '',
		self::ELASTIC_INDEX => ''
	];

	/** @var IConfig */
	private $config;

	/** @var string */
	private $userId;

	/** @var MiscService */
	private $miscService;


	/**
	 * ConfigService constructor.
	 *
	 * @param IConfig $config
	 * @param string $userId
	 * @param MiscService $miscService
	 */
	public function __construct(IConfig $config, $userId, MiscService $miscService) {
		$this->config = $config;
		$this->userId = $userId;
		$this->miscService = $miscService;
	}


	/**
	 * @return array
	 */
	public function getConfig() {
		$keys = array_keys($this->defaults);
		$data = [];

		foreach ($keys as $k) {
			$data[$k] = $this->getAppValue($k);
		}

		return $data;
	}


	/**
	 * @param array $save
	 */
	public function setConfig($save) {
		$keys = array_keys($this->defaults);

		foreach ($keys as $k) {
			if (array_key_exists($k, $save)) {
				$this->setAppValue($k, $save[$k]);
			}
		}
	}


	/**
	 * @return string
	 * @throws ConfigurationException
	 */
	public function getElasticHost() {

		$host = $this->getAppValue(self::ELASTIC_HOST);
		if ($host === '') {
			throw new ConfigurationException('Your ElasticSearchPlatform is not configured properly');
		}

		return $host;
	}


	/**
	 * @return string
	 * @throws ConfigurationException
	 */
	public function getElasticIndex() {

		$index = $this->getAppValue(self::ELASTIC_INDEX);
		if ($index === '') {
			throw new ConfigurationException('Your ElasticSearchPlatform is not configured properly');
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
	public function getAppValue($key) {
		$defaultValue = null;
		if (array_key_exists($key, $this->defaults)) {
			$defaultValue = $this->defaults[$key];
		}

		return $this->config->getAppValue(Application::APP_NAME, $key, $defaultValue);
	}

	/**
	 * Set a value by key
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return void
	 */
	public function setAppValue($key, $value) {
		$this->config->setAppValue(Application::APP_NAME, $key, $value);
	}

	/**
	 * remove a key
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function deleteAppValue($key) {
		return $this->config->deleteAppValue(Application::APP_NAME, $key);
	}

	/**
	 * Get a user value by key
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function getUserValue($key) {
		$defaultValue = null;
		if (array_key_exists($key, $this->defaults)) {
			$defaultValue = $this->defaults[$key];
		}

		return $this->config->getUserValue(
			$this->userId, Application::APP_NAME, $key, $defaultValue
		);
	}

	/**
	 * Set a user value by key
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 * @throws PreConditionNotMetException
	 */
	public function setUserValue($key, $value) {
		return $this->config->setUserValue($this->userId, Application::APP_NAME, $key, $value);
	}

	/**
	 * Get a user value by key and user
	 *
	 * @param string $userId
	 * @param string $key
	 *
	 * @return string
	 */
	public function getValueForUser($userId, $key) {
		return $this->config->getUserValue($userId, Application::APP_NAME, $key);
	}

	/**
	 * Set a user value by key
	 *
	 * @param string $userId
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 * @throws PreConditionNotMetException
	 */
	public function setValueForUser($userId, $key, $value) {
		return $this->config->setUserValue($userId, Application::APP_NAME, $key, $value);
	}

	/**
	 * return the cloud version.
	 * if $complete is true, return a string x.y.z
	 *
	 * @param boolean $complete
	 *
	 * @return string|integer
	 */
	public function getCloudVersion($complete = false) {
		$ver = Util::getVersion();

		if ($complete) {
			return implode('.', $ver);
		}

		return $ver[0];
	}
}
