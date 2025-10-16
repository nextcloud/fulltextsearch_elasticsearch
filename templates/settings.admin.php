<?php
declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

use OCA\FullTextSearch_Elasticsearch\AppInfo\Application;
use OCP\Util;


Util::addScript(Application::APP_NAME, 'admin.elements');
Util::addScript(Application::APP_NAME, 'admin.settings');
Util::addScript(Application::APP_NAME, 'admin');

Util::addStyle(Application::APP_NAME, 'admin');

?>

<div id="elastic_search" class="section" style="display: none;">
	<h2><?php p($l->t('Elastic Search')) ?></h2>

	<div class="div-table">

		<div class="div-table-row">
			<div class="div-table-col div-table-col-left">
				<span class="leftcol"><?php p($l->t('Address of the Servlet')); ?>:</span>
				<br/>
				<em><?php p($l->t('Include your credential in case authentication is required.')); ?></em>
			</div>
			<div class="div-table-col">
				<input type="text" id="elasticsearch_host"
					   placeholder="http://username:password@localhost:9200/"/>
			</div>
		</div>

		<div class="div-table-row">
			<div class="div-table-col div-table-col-left">
				<span class="leftcol"><?php p($l->t('Index')); ?>:</span>
				<br/>
				<em><?php p($l->t('Name of your index.')); ?></em>
			</div>
			<div class="div-table-col">
				<input type="text" id="elasticsearch_index" placeholder="my_index"/>
			</div>
		</div>

		<div class="div-table-row">
			<div class="div-table-col div-table-col-left">
				<span class="leftcol"><?php p($l->t('[Advanced] Analyzer tokenizer')); ?>:</span>
				<br/>
				<em><?php p($l->t('Some language might need a specific tokenizer.')); ?></em>
			</div>
			<div class="div-table-col">
				<input type="text" id="analyzer_tokenizer" />
			</div>
		</div>


	</div>


</div>
