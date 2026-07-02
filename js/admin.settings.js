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

		elasticsearch_settings.request('GET', '/apps/fulltextsearch_elasticsearch/admin/settings').then(function (res) {
			elasticsearch_settings.updateSettingPage(res);
		});

	},

	/** @namespace result.elastic_host */
	/** @namespace result.elastic_index */
	updateSettingPage: function (result) {

		elasticsearch_elements.elasticsearch_host.value = result.elastic_host;
		elasticsearch_elements.elasticsearch_index.value = result.elastic_index;
		elasticsearch_elements.analyzer_tokenizer.value = result.analyzer_tokenizer;

		fts_admin_settings.tagSettingsAsSaved(elasticsearch_elements.elasticsearch_div);
	},


	saveSettings: function () {

		var data = {
			elastic_host: elasticsearch_elements.elasticsearch_host.value,
			elastic_index: elasticsearch_elements.elasticsearch_index.value,
			analyzer_tokenizer: elasticsearch_elements.analyzer_tokenizer.value
		};

		elasticsearch_settings.request('POST', '/apps/fulltextsearch_elasticsearch/admin/settings', data).then(function (res) {
			elasticsearch_settings.updateSettingPage(res);
		});

	},


	request: function (method, route, data) {
		var options = {
			method: method,
			credentials: 'same-origin',
			headers: {
				'Accept': 'application/json',
				'requesttoken': window.OC ? window.OC.requestToken : ''
			}
		};

		if (method === 'POST') {
			options.headers['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
			options.body = elasticsearch_settings.encodeData(data);
		}

		return fetch(window.OC.generateUrl(route), options).then(function (response) {
			if (!response.ok) {
				throw new Error('Request failed: ' + response.status);
			}

			return response.json();
		});
	},


	encodeData: function (data) {
		var params = new URLSearchParams();
		Object.keys(data).forEach(function (key) {
			params.append('data[' + key + ']', data[key]);
		});

		return params.toString();
	}


};
