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
/** global: fns_admin_settings */
/** global: elasticsearch_settings */


var elasticsearch_elements = {
	elasticsearch_div: null,
	elasticsearch_host: null,
	elasticsearch_index: null,


	init: function () {
		elasticsearch_elements.elasticsearch_div = $('#elastic_search');
		elasticsearch_elements.elasticsearch_host = $('#elasticsearch_host');
		elasticsearch_elements.elasticsearch_index = $('#elasticsearch_index');

		elasticsearch_elements.elasticsearch_host.on('input', function () {
			fns_admin_settings.tagSettingsAsNotSaved($(this));
		}).blur(function () {
			elasticsearch_settings.saveSettings();
		});

		elasticsearch_elements.elasticsearch_index.on('input', function () {
			fns_admin_settings.tagSettingsAsNotSaved($(this));
		}).blur(function () {
			elasticsearch_settings.saveSettings();
		});
	}


};


