/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

export interface IAdminSettingsConfig {
	elastic_host: string
	elastic_index: string
	analyzer_tokenizer: string
}

/**
 * Detail payload of the `fulltextsearch:settings-admin-updated` window event, broadcast by the
 * main fulltextsearch app's admin settings page. See its src/constants.ts for the full contract.
 */
export interface ISettingsUpdatedEventDetail {
	platform: string
	providers: string[]
}
