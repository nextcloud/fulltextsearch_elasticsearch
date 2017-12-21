<?php

use OCA\FullNextSearch_ElasticSearch\AppInfo\Application;
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
				<span class="leftcol">Address of the Servlet:</span>
				<br/>
				<em>Include your credential in case authentication is required.</em>
			</div>
			<div class="div-table-col">
				<input type="text" id="elasticsearch_host"
					   placeholder="http://username:password@localhost:9200/" class="hidden"/>
			</div>
		</div>

		<div class="div-table-row">
			<div class="div-table-col div-table-col-left">
				<span class="leftcol">Index :</span>
				<br/>
				<em>Name of your index.</em>
			</div>
			<div class="div-table-col">
				<input type="text" id="elasticsearch_index" placeholder="my_index" class="hidden"/>
			</div>
		</div>

	</div>


</div>