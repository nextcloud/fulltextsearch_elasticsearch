<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Controller;

use Exception;
use OCA\FullTextSearch_Elasticsearch\AppInfo\Application;
use OCA\FullTextSearch_Elasticsearch\Service\ConfigService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;


/**
 * Class SettingsController
 *
 * @package OCA\FullTextSearch_Elasticsearch\Controller
 */
class SettingsController extends Controller {

	public function __construct(
		IRequest $request,
		private ConfigService $configService
	) {
		parent::__construct(Application::APP_NAME, $request);
	}

	/**
	 * @return DataResponse
	 * @throws Exception
	 */
	public function getSettingsAdmin(): DataResponse {
		$data = $this->configService->getConfig();

		return new DataResponse($data, Http::STATUS_OK);
	}

	/**
	 * @param array $data
	 *
	 * @return DataResponse
	 * @throws Exception
	 */
	public function setSettingsAdmin(array $data): DataResponse {
		if ($this->configService->checkConfig($data)) {
			$this->configService->setConfig($data);
		}

		return $this->getSettingsAdmin();
	}
}

