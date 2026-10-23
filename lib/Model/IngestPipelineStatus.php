<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Model;

enum IngestPipelineStatus: string {
	/** no pipeline on Elasticsearch */
	case MISSING = 'missing';

	/** pipeline with a different structure, documents must be indexed again once it is updated */
	case INCOMPATIBLE = 'incompatible';

	/** pipeline from a previous version, updated on the next index run */
	case OUTDATED = 'outdated';

	case CURRENT = 'current';
}
