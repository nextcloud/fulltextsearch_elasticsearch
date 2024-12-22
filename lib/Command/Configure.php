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

namespace OCA\FullTextSearch_OpenSearch\Command;

use Exception;
use OC\Core\Command\Base;
use OCA\FullTextSearch_OpenSearch\Service\ConfigService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Configure extends Base {

    /**
     * Constructor method for the class.
     *
     * @param ConfigService $configService Instance of ConfigService.
     * @return void
     */
    public function __construct(
		private ConfigService $configService,
	) {
		parent::__construct();
	}

    /**
     * Configures the command with its name, arguments, and description.
     *
     * @return void
     */
    protected function configure() {
		parent::configure();
		$this->setName('fulltextsearch_opensearch:configure')
			->addArgument('json', InputArgument::REQUIRED, 'set config')
			->setDescription('Configure the installation');
	}

    /**
     * Executes the command to process and update configuration values.
     *
     * @param InputInterface $input The input interface containing the command arguments.
     * @param OutputInterface $output The output interface used for writing responses.
     * @return int Returns 0 on successful execution, or 1 if the input JSON is invalid.
     */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$json = $input->getArgument('json');

		$config = json_decode($json, true);

		if (!is_array($config)) {
			$output->writeln('Invalid JSON');

			return 1;
		}

		$ak = array_keys($config);
		foreach ($ak as $k) {
			if (array_key_exists($k, ConfigService::$defaults)) {
				$this->configService->setAppValue($k, $config[$k]);
			}
		}

		$output->writeln(json_encode($this->configService->getConfig(), JSON_PRETTY_PRINT));

		return 0;
	}
}
