<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<NcSettingsSection
		v-show="visible"
		:name="t('fulltextsearch_elasticsearch', 'Elastic Search')">
		<NcFormBox>
			<NcTextField
				v-model="config.elastic_host"
				:label="t('fulltextsearch_elasticsearch', 'Address of the Servlet')"
				:helperText="t('fulltextsearch_elasticsearch', 'Include your credential in case authentication is required.')"
				placeholder="http://username:password@localhost:9200/"
				@blur="saveSettings" />

			<NcTextField
				v-model="config.elastic_index"
				:label="t('fulltextsearch_elasticsearch', 'Index')"
				:helperText="t('fulltextsearch_elasticsearch', 'Name of your index.')"
				placeholder="my_index"
				@blur="saveSettings" />

			<NcTextField
				v-model="config.analyzer_tokenizer"
				:label="t('fulltextsearch_elasticsearch', '[Advanced] Analyzer tokenizer')"
				:helperText="t('fulltextsearch_elasticsearch', 'Some language might need a specific tokenizer.')"
				@blur="saveSettings" />
		</NcFormBox>
	</NcSettingsSection>
</template>

<script setup lang="ts">
import type { IAdminSettingsConfig, ISettingsUpdatedEventDetail } from './types.d.ts'

import axios from '@nextcloud/axios'
import { loadState } from '@nextcloud/initial-state'
import { t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import NcFormBox from '@nextcloud/vue/components/NcFormBox'
import NcSettingsSection from '@nextcloud/vue/components/NcSettingsSection'
import NcTextField from '@nextcloud/vue/components/NcTextField'
import { ELASTICSEARCH_PLATFORM_ID, SETTINGS_UPDATED_EVENT } from './constants.ts'
import { logger } from './logger.ts'

const config = ref(loadState<IAdminSettingsConfig>('fulltextsearch_elasticsearch', 'adminConfig'))
const visible = ref(window.OCA?.FullTextSearch?.settings?.platform === ELASTICSEARCH_PLATFORM_ID)

/**
 * Show or hide this section based on the platform selected on the main fulltextsearch app's admin
 * settings page.
 *
 * @param detail Event detail, or the value of window.OCA.FullTextSearch.settings.
 */
function onSettingsUpdated(detail: ISettingsUpdatedEventDetail): void {
	visible.value = detail.platform === ELASTICSEARCH_PLATFORM_ID
}

/**
 * @param event The fulltextsearch:settings-admin-updated CustomEvent.
 */
function handleSettingsUpdatedEvent(event: Event): void {
	onSettingsUpdated((event as CustomEvent<ISettingsUpdatedEventDetail>).detail)
}

onMounted(() => {
	window.addEventListener(SETTINGS_UPDATED_EVENT, handleSettingsUpdatedEvent)
})

onBeforeUnmount(() => {
	window.removeEventListener(SETTINGS_UPDATED_EVENT, handleSettingsUpdatedEvent)
})

/**
 * Persist the settings on the backend and refresh local state from the response.
 */
async function saveSettings(): Promise<void> {
	try {
		const { data } = await axios.post<IAdminSettingsConfig>(generateUrl('/apps/fulltextsearch_elasticsearch/admin/settings'), {
			data: {
				elastic_host: config.value.elastic_host,
				elastic_index: config.value.elastic_index,
				analyzer_tokenizer: config.value.analyzer_tokenizer,
			},
		})
		config.value = data
	} catch (error) {
		logger.error('Failed to save Elasticsearch settings', { error })
	}
}
</script>
