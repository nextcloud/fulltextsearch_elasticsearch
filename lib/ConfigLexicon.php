<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch;

use OCP\Config\Lexicon\Entry;
use OCP\Config\Lexicon\ILexicon;
use OCP\Config\Lexicon\Strictness;
use OCP\Config\ValueType;

/**
 * Config Lexicon for fulltextsearch_elasticsearch.
 *
 * Please Add & Manage your Config Keys in that file and keep the Lexicon up to date!
 */
class ConfigLexicon implements ILexicon {
	public const FIELDS_LIMIT = 'fields_limit';
	public const ELASTIC_HOST = 'elastic_host';
	public const ELASTIC_INDEX = 'elastic_index';
	public const ELASTIC_LOGGER_ENABLED = 'elastic_logger_enabled';
	public const ANALYZER_TOKENIZER = 'analyzer_tokenizer';
	public const ALLOW_SELF_SIGNED_CERT = 'allow_self_signed_cert';

	public function getStrictness(): Strictness {
		return Strictness::NOTICE;
	}

	public function getAppConfigs(): array {
		return [
			new Entry(key: self::FIELDS_LIMIT, type: ValueType::INT, defaultRaw: 10000, definition: 'Maximum number of fields in the index map', lazy: true),
			new Entry(key: self::ELASTIC_HOST, type: ValueType::STRING, defaultRaw: '', definition: 'Address of the elasticsearch', lazy: true),
			new Entry(key: self::ELASTIC_INDEX, type: ValueType::STRING, defaultRaw: '', definition: 'Name of the index on elasticsearch', lazy: true),
			new Entry(key: self::ELASTIC_LOGGER_ENABLED, type: ValueType::BOOL, defaultRaw: false, definition: 'Allow 3rd-party elasticsearch-php to write in nextcloud.log', lazy: true, note: 'Be aware that if your nextcloud log level is set to DEBUG (0), clear version of the credentials used to the remote elasticsearch could end up in your logs'),
			new Entry(key: self::ANALYZER_TOKENIZER, type: ValueType::STRING, defaultRaw: 'standard', definition: 'used analyzer tokenizer', lazy: true),
			new Entry(key: self::ALLOW_SELF_SIGNED_CERT, type: ValueType::BOOL, defaultRaw: false, definition: 'allow self signed certificate', lazy: true),
		];
	}

	public function getUserConfigs(): array {
		return [];
	}
}
