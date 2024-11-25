/*
 * FullTextSearch_OpenSearch - Use OpenSearch to index the content of your nextcloud
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2018
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
 */

/** global: OCA */
/** global: fts_admin_settings */
/** global: opensearch_settings */


var opensearch_elements = {
	opensearch_div: null,
	opensearch_host: null,
	opensearch_index: null,
	analyzer_tokenizer: null,


	init: function () {
		opensearch_elements.opensearch_div = $('#elastic_search');
		opensearch_elements.opensearch_host = $('#opensearch_host');
		opensearch_elements.opensearch_index = $('#opensearch_index');
		opensearch_elements.analyzer_tokenizer = $('#analyzer_tokenizer');

		opensearch_elements.opensearch_host.on('input', function () {
			fts_admin_settings.tagSettingsAsNotSaved($(this));
		}).blur(function () {
			opensearch_settings.saveSettings();
		});

		opensearch_elements.opensearch_index.on('input', function () {
			fts_admin_settings.tagSettingsAsNotSaved($(this));
		}).blur(function () {
			opensearch_settings.saveSettings();
		});

		opensearch_elements.analyzer_tokenizer.on('input', function () {
			fts_admin_settings.tagSettingsAsNotSaved($(this));
		}).blur(function () {
			opensearch_settings.saveSettings();
		});
	}


};


