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

/** global: OC */
/** global: opensearch_elements */
/** global: fts_admin_settings */




var opensearch_settings = {

	config: null,

	refreshSettingPage: function () {

		$.ajax({
			method: 'GET',
			url: OC.generateUrl('/apps/fulltextsearch_opensearch/admin/settings')
		}).done(function (res) {
			opensearch_settings.updateSettingPage(res);
		});

	},

	/** @namespace result.opensearch_host */
	/** @namespace result.opensearch_index */
	updateSettingPage: function (result) {

		opensearch_elements.opensearch_host.val(result.opensearch_host);
		opensearch_elements.opensearch_index.val(result.opensearch_index);
		opensearch_elements.analyzer_tokenizer.val(result.analyzer_tokenizer);

		fts_admin_settings.tagSettingsAsSaved(opensearch_elements.opensearch_div);
	},


	markInputField: function (input, mark=true) {
		input.css('border-color', mark ? '#d00' : '#ccc');
	},

	saveSettings: function () {

		var data = {
			opensearch_host: opensearch_elements.opensearch_host.val(),
			opensearch_index: opensearch_elements.opensearch_index.val(),
			analyzer_tokenizer: opensearch_elements.analyzer_tokenizer.val()
		};

		if(data.opensearch_host === '') {
			opensearch_settings.markInputField(opensearch_elements.opensearch_host, true);
		}
		if(data.opensearch_index === '') {
			opensearch_settings.markInputField(opensearch_elements.opensearch_index, true);
		}

		if( data.opensearch_host !== '' && data.opensearch_index !== '' ) {
			$.ajax({
				method: 'POST',
				url: OC.generateUrl('/apps/fulltextsearch_opensearch/admin/settings'),
				data: {
					data: data
				}
			}).done(function (res, textStatus, xhr) {
				opensearch_settings.updateSettingPage(res);
				opensearch_settings.markInputField(opensearch_elements.opensearch_host, false);
				opensearch_settings.markInputField(opensearch_elements.opensearch_index, false);
			}).fail(function (xhr, textStatus, errorThrown) {
				if (xhr.responseJSON instanceof Array) {
					xhr.responseJSON.forEach(function (value, index, array) {
						opensearch_settings.markInputField($('#' + value), true);
					});
				}
			});
		}
	}


};
