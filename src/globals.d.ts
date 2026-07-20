/// <reference types="@nextcloud/typings" />
/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
declare global {
	interface Window {
		OC: Nextcloud.v32.OC
		OCP: Nextcloud.v32.OCP
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		OCA: any
	}
}

export {}
