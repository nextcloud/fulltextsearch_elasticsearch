/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

/** global: OCA */
/** global: elasticsearch_elements */
/** global: elasticsearch_settings */

function ready(callback) {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', callback);
	} else {
		callback();
	}
}


ready(function () {


	/**
	 * @constructs ElasticSearchAdmin
	 */
	var ElasticSearchAdmin = function () {
		Object.assign(ElasticSearchAdmin.prototype, elasticsearch_elements, elasticsearch_settings);

		elasticsearch_elements.init();
		elasticsearch_settings.refreshSettingPage();
	};

	window.OCA = window.OCA || {};
	window.OCA.FullTextSearchAdmin = window.OCA.FullTextSearchAdmin || {};
	window.OCA.FullTextSearchAdmin.elasticSearch = ElasticSearchAdmin;
	window.OCA.FullTextSearchAdmin.elasticSearch.settings = new ElasticSearchAdmin();

});
