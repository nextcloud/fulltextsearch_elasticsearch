/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

/** global: OCA */
/** global: elasticsearch_elements */
/** global: elasticsearch_settings */


$(document).ready(function () {


	/**
	 * @constructs ElasticSearchAdmin
	 */
	var ElasticSearchAdmin = function () {
		$.extend(ElasticSearchAdmin.prototype, elasticsearch_elements);
		$.extend(ElasticSearchAdmin.prototype, elasticsearch_settings);

		elasticsearch_elements.init();
		elasticsearch_settings.refreshSettingPage();
	};

	OCA.FullTextSearchAdmin.elasticSearch = ElasticSearchAdmin;
	OCA.FullTextSearchAdmin.elasticSearch.settings = new ElasticSearchAdmin();

});
