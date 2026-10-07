<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Migration;

use Closure;
use OCA\FullTextSearch_Elasticsearch\AppInfo\Application;
use OCA\FullTextSearch_Elasticsearch\ConfigLexicon;
use OCP\IAppConfig;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Flag an already stored elastic_host as sensitive, as it may contain credentials.
 */
class Version36000Date20261007000000 extends SimpleMigrationStep {
	public function __construct(
		private readonly IAppConfig $appConfig,
	) {
	}

	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
		if ($this->appConfig->hasKey(Application::APP_NAME, ConfigLexicon::ELASTIC_HOST, true)) {
			$this->appConfig->updateSensitive(Application::APP_NAME, ConfigLexicon::ELASTIC_HOST, true);
		}
	}
}
