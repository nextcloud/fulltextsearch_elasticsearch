/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

/** global: OC */
/** global: elasticsearch_elements */
/** global: fts_admin_settings */




var elasticsearch_settings = {

	config: null,

	refreshSettingPage: function () {

		$.ajax({
			method: 'GET',
			url: OC.generateUrl('/apps/fulltextsearch_elasticsearch/admin/settings')
		}).done(function (res) {
			elasticsearch_settings.updateSettingPage(res);
		});

	},

	/** @namespace result.elastic_host */
	/** @namespace result.elastic_index */
	updateSettingPage: function (result) {

		elasticsearch_elements.elasticsearch_host.val(result.elastic_host);
		elasticsearch_elements.elasticsearch_index.val(result.elastic_index);
		elasticsearch_elements.analyzer_tokenizer.val(result.analyzer_tokenizer);

		fts_admin_settings.tagSettingsAsSaved(elasticsearch_elements.elasticsearch_div);
	},


	saveSettings: function () {

		var data = {
			elastic_host: elasticsearch_elements.elasticsearch_host.val(),
			elastic_index: elasticsearch_elements.elasticsearch_index.val(),
			analyzer_tokenizer: elasticsearch_elements.analyzer_tokenizer.val()
		};

		$.ajax({
			method: 'POST',
			url: OC.generateUrl('/apps/fulltextsearch_elasticsearch/admin/settings'),
			data: {
				data: data
			}
		}).done(function (res) {
			elasticsearch_settings.updateSettingPage(res);
		});

	}


};
