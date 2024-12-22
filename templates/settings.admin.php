<?php
declare(strict_types=1);


/**
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



use OCA\FullTextSearch_OpenSearch\AppInfo\Application;
use OCA\FullTextSearch_OpenSearch\Service\ConfigService;
use OCP\Util;

// Asset constants
const ADMIN_ELEMENTS_SCRIPT = 'admin.elements';
const ADMIN_SETTINGS_SCRIPT = 'admin.settings';
const ADMIN_SCRIPT = 'admin';
const ADMIN_STYLE = 'admin';

// Placeholder constants
const PLACEHOLDER_HOST = 'http://username:password@localhost:9200/';
const PLACEHOLDER_INDEX = 'my_index';

// Add assets
function addAssets(string $appName): void
{
    Util::addScript($appName, ADMIN_ELEMENTS_SCRIPT);
    Util::addScript($appName, ADMIN_SETTINGS_SCRIPT);
    Util::addScript($appName, ADMIN_SCRIPT);
    Util::addStyle($appName, ADMIN_STYLE);
}

// Add necessary scripts and styles
addAssets(Application::APP_NAME);
?>
<div id="open_search" class="section hidden-section">
    <h2><?php p($l->t('Open Search')) ?></h2>
    <div class="div-table">

        <div class="div-table-row">
            <div class="div-table-col div-table-col-left">
                <span class="leftcol"><?php p($l->t('Address of the Servlet')); ?>:</span>
                <br/>
                <em><?php p($l->t('Include your credential in case authentication is required.')); ?></em>
            </div>
            <div class="div-table-col">
                <input type="text" id="<?php echo ConfigService::OPENSEARCH_HOST; ?>" placeholder="<?php p(PLACEHOLDER_HOST) ?>"/>
            </div>
        </div>

        <div class="div-table-row">
            <div class="div-table-col div-table-col-left">
                <span class="leftcol"><?php p($l->t('Index')); ?>:</span>
                <br/>
                <em><?php p($l->t('Name of your index.')); ?></em>
            </div>
            <div class="div-table-col">
                <input type="text" id="<?php echo ConfigService::OPENSEARCH_INDEX; ?>" placeholder="<?php p(PLACEHOLDER_INDEX) ?>"/>
            </div>
        </div>

        <div class="div-table-row">
            <div class="div-table-col div-table-col-left">
                <span class="leftcol"><?php p($l->t('[Advanced] Analyzer tokenizer')); ?>:</span>
                <br/>
                <em><?php p($l->t('Some language might needs a specific tokenizer.')); ?></em>
            </div>
            <div class="div-table-col">
                <input type="text" id="<?php echo ConfigService::ANALYZER_TOKENIZER; ?>"/>
            </div>
        </div>

    </div>
</div>