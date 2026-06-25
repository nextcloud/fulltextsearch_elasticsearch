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
		elasticsearch_elements.elasticsearch_div = document.getElementById('elastic_search');
		elasticsearch_elements.elasticsearch_host = document.getElementById('elasticsearch_host');
		elasticsearch_elements.elasticsearch_index = document.getElementById('elasticsearch_index');
		elasticsearch_elements.analyzer_tokenizer = document.getElementById('analyzer_tokenizer');

		elasticsearch_elements.bindInput(elasticsearch_elements.elasticsearch_host);
		elasticsearch_elements.bindInput(elasticsearch_elements.elasticsearch_index);
		elasticsearch_elements.bindInput(elasticsearch_elements.analyzer_tokenizer);
	},


	bindInput: function (element) {
		element.addEventListener('input', function () {
			fts_admin_settings.tagSettingsAsNotSaved(this);
		});
		element.addEventListener('blur', function () {
			elasticsearch_settings.saveSettings();
		});
	}
};

