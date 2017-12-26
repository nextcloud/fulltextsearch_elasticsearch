/*
 * FullNextSearch - Full Text Search your Nextcloud.
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2017
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 */

/** global: OCA */
/** global: elasticsearch_elements */
/** global: fns_admin_settings */




var elasticsearch_settings = {

	config: null,

	refreshSettingPage: function () {

		$.ajax({
			method: 'GET',
			url: OC.generateUrl('/apps/fullnextsearch_elasticsearch/admin/settings')
		}).done(function (res) {
			elasticsearch_settings.updateSettingPage(res);
		});

	},

	/** @namespace result.elastic_host */
	/** @namespace result.elastic_index */
	updateSettingPage: function (result) {

		elasticsearch_elements.elasticsearch_host.val(result.elastic_host);
		elasticsearch_elements.elasticsearch_index.val(result.elastic_index);

		fns_admin_settings.tagSettingsAsSaved(elasticsearch_elements.elasticsearch_div);
	},


	saveSettings: function () {

		var data = {
			elastic_host: elasticsearch_elements.elasticsearch_host.val(),
			elastic_index: elasticsearch_elements.elasticsearch_index.val()
		};

		$.ajax({
			method: 'POST',
			url: OC.generateUrl('/apps/fullnextsearch_elasticsearch/admin/settings'),
			data: {
				data: data
			}
		}).done(function (res) {
			elasticsearch_settings.updateSettingPage(res);
		});

	}


};
