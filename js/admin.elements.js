/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

/** global: OCA */
/** global: fts_admin_settings */
/** global: elasticsearch_settings */


var elasticsearch_elements = {
	elasticsearch_div: null,
	elasticsearch_host: null,
	elasticsearch_index: null,
	analyzer_tokenizer: null,


	init: function () {
		elasticsearch_elements.elasticsearch_div = $('#elastic_search');
		elasticsearch_elements.elasticsearch_host = $('#elasticsearch_host');
		elasticsearch_elements.elasticsearch_index = $('#elasticsearch_index');
		elasticsearch_elements.analyzer_tokenizer = $('#analyzer_tokenizer');

		elasticsearch_elements.elasticsearch_host.on('input', function () {
			fts_admin_settings.tagSettingsAsNotSaved($(this));
		}).blur(function () {
			elasticsearch_settings.saveSettings();
		});

		elasticsearch_elements.elasticsearch_index.on('input', function () {
			fts_admin_settings.tagSettingsAsNotSaved($(this));
		}).blur(function () {
			elasticsearch_settings.saveSettings();
		});

		elasticsearch_elements.analyzer_tokenizer.on('input', function () {
			fts_admin_settings.tagSettingsAsNotSaved($(this));
		}).blur(function () {
			elasticsearch_settings.saveSettings();
		});
	}


};


