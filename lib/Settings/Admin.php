<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Settings;

use Exception;
use OCA\FullTextSearch_Elasticsearch\AppInfo\Application;
use OCA\FullTextSearch_Elasticsearch\Service\ConfigService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\Settings\ISettings;
use OCP\Util;

class Admin implements ISettings {

	public function __construct(
		private ConfigService $configService,
		private IInitialState $initialStateService,
	) {
	}

	/**
	 * @return TemplateResponse
	 * @throws Exception
	 */
	public function getForm(): TemplateResponse {
		$this->initialStateService->provideInitialState('adminConfig', $this->configService->getConfig());

		Util::addScript(Application::APP_NAME, 'fulltextsearch_elasticsearch-settings-admin');
		Util::addStyle(Application::APP_NAME, 'fulltextsearch_elasticsearch-settings-admin');

		return new TemplateResponse(Application::APP_NAME, 'settings.admin', []);
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection(): string {
		return 'fulltextsearch';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * keep the server setting at the top, right after "server settings"
	 */
	public function getPriority(): int {
		return 31;
	}
}
