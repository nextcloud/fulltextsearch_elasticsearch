<?php
/**
 * FullNextSearch - Full Text Search your Nextcloud.
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
 *
 */

namespace OCA\FullNextSearch_ElasticSearch\Controller;

use OCA\FullNextSearch_ElasticSearch\AppInfo\Application;
use OCA\FullNextSearch_ElasticSearch\Service\ConfigService;
use OCA\FullNextSearch_ElasticSearch\Service\MiscService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class SettingsController extends Controller {

	/** @var ConfigService */
	private $configService;

	/** @var MiscService */
	private $miscService;


	/**
	 * NavigationController constructor.
	 *
	 * @param IRequest $request
	 * @param ConfigService $configService
	 * @param MiscService $miscService
	 */
	function __construct(IRequest $request, ConfigService $configService, MiscService $miscService) {
		parent::__construct(Application::APP_NAME, $request);
		$this->configService = $configService;
		$this->miscService = $miscService;
	}


	/**
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function getSettingsPersonal() {
		$data = [];

		return new DataResponse($data, Http::STATUS_OK);
	}

	/**
	 * @param $data
	 *
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function setSettingsPersonal($data) {
//		$this->configService->setUserValue(
//			ConfigService::APP_TEST_PERSONAL, $data[ConfigService::APP_TEST_PERSONAL]
//		);

		return $this->getSettingsAdmin();
	}


	/**
	 * @return DataResponse
	 */
	public function getSettingsAdmin() {
		$data = [];

		return new DataResponse($data, Http::STATUS_OK);
	}

	/**
	 * @param $data
	 *
	 * @return DataResponse
	 */
	public function setSettingsAdmin($data) {
//		$this->configService->setAppValue(
//
//		);

		return $this->getSettingsAdmin();
	}

}