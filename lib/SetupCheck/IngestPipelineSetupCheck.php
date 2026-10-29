<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\SetupCheck;

use OCA\FullTextSearch_Elasticsearch\ConfigLexicon;
use OCA\FullTextSearch_Elasticsearch\Model\IngestPipelineStatus;
use OCA\FullTextSearch_Elasticsearch\Platform\ElasticSearchPlatform;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IL10N;
use OCP\SetupCheck\ISetupCheck;
use OCP\SetupCheck\SetupResult;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Check the ingest pipeline used to extract the content of documents.
 */
class IngestPipelineSetupCheck implements ISetupCheck {
	private const REINDEX_COMMAND = 'occ fulltextsearch:index \'{"force":true}\'';

	public function __construct(
		private readonly IL10N $l10n,
		private readonly IAppConfig $appConfig,
		private readonly ElasticSearchPlatform $platform,
		private readonly LoggerInterface $logger,
	) {
	}

	public function getCategory(): string {
		return 'fulltextsearch';
	}

	public function getName(): string {
		return $this->l10n->t('Elasticsearch content extraction');
	}

	public function run(): SetupResult {
		if ($this->appConfig->getAppValueString(ConfigLexicon::ELASTIC_HOST) === '') {
			return SetupResult::success($this->l10n->t('Elasticsearch is not configured.'));
		}

		try {
			$this->platform->loadPlatform();
			$status = $this->platform->getIngestPipelineStatus();

			if ($status === IngestPipelineStatus::CURRENT
				&& $this->appConfig->getAppValueBool(ConfigLexicon::REINDEX_REQUIRED)) {
				$count = $this->platform->countDocumentsFromOutdatedPipeline();
				if ($count === 0) {
					$this->appConfig->deleteAppValue(ConfigLexicon::REINDEX_REQUIRED);
				} else {
					return SetupResult::warning(
						$this->l10n->n(
							'%n document needs to be indexed again to be searchable by its content. Run "%s".',
							'%n documents need to be indexed again to be searchable by their content. Run "%s".',
							$count,
							[self::REINDEX_COMMAND]
						)
					);
				}
			}
		} catch (Throwable $e) {
			$this->logger->warning('Could not check the Elasticsearch ingest pipeline', ['exception' => $e]);

			return SetupResult::warning(
				$this->l10n->t('Could not check the ingest pipeline on Elasticsearch: %s', [$e->getMessage()])
			);
		}

		return match ($status) {
			IngestPipelineStatus::MISSING => SetupResult::warning(
				$this->l10n->t('The ingest pipeline is missing on Elasticsearch, the content of documents cannot be indexed. Create it by running "%s".', ['occ fulltextsearch:index'])
			),
			IngestPipelineStatus::INCOMPATIBLE => SetupResult::warning(
				$this->l10n->t('The ingest pipeline on Elasticsearch is outdated. Update it and index all documents again by running "%s".', [self::REINDEX_COMMAND])
			),
			IngestPipelineStatus::OUTDATED => SetupResult::success(
				$this->l10n->t('The ingest pipeline on Elasticsearch will be updated on the next run of "%s".', ['occ fulltextsearch:index'])
			),
			IngestPipelineStatus::CURRENT => SetupResult::success(),
		};
	}
}
