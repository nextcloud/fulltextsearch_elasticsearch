<?php
declare(strict_types=1);


/**
 * FullTextSearch_ElasticSearch - Use Elasticsearch to index the content of your nextcloud
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


namespace OCA\FullTextSearch_ElasticSearch\Model;


use JsonSerializable;


/**
 * Class QueryContent
 *
 * @package OCA\FullTextSearch_ElasticSearch\Model
 */
class QueryContent implements JsonSerializable {


	const OPTION_MUST = 1;
	const OPTION_MUST_NOT = 2;


	/** @var string */
	private $word;

	/** @var string */
	private $should;

	/** @var string */
	private $match;

	/** @var int */
	private $option = 0;


	/** @var array */
	private $options = [
		'+' => [self::OPTION_MUST, 'must', 'match_phrase_prefix'],
		'-' => [self::OPTION_MUST_NOT, 'must_not', 'match_phrase_prefix']
	];


	/**
	 * QueryContent constructor.
	 *
	 * @param string $word
	 */
	function __construct(string $word) {
		$this->word = $word;

		$this->init();
	}


	/**
	 *
	 */
	private function init() {
		$this->setShould('should');
		$this->setMatch('match_phrase_prefix');

		$curr = substr($this->getWord(), 0, 1);

		if (array_key_exists($curr, $this->options)) {
			$this->setOption($this->options[$curr][0])
				 ->setShould($this->options[$curr][1])
				 ->setMatch($this->options[$curr][2])
				 ->setWord(substr($this->getWord(), 1));
		}

		if (substr($this->getWord(), 0, 1) === '"') {
			$this->setMatch('match');
			if (strpos($this->getWord(), " ") > -1) {
				$this->setMatch('match_phrase_prefix');
			}
		}

		$this->setWord(str_replace('"', '', $this->getWord()));
	}


	/**
	 * @return string
	 */
	public function getWord(): string {
		return $this->word;
	}

	/**
	 * @param string $word
	 *
	 * @return $this
	 */
	public function setWord(string $word): QueryContent {
		$this->word = $word;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getShould(): string {
		return $this->should;
	}

	/**
	 * @param string $should
	 *
	 * @return QueryContent
	 */
	public function setShould(string $should): QueryContent {
		$this->should = $should;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getMatch(): string {
		return $this->match;
	}

	/**
	 * @param string $match
	 *
	 * @return QueryContent
	 */
	public function setMatch(string $match): QueryContent {
		$this->match = $match;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getOption(): int {
		return $this->option;
	}

	/**
	 * @param int $option
	 *
	 * @return QueryContent
	 */
	public function setOption(int $option): QueryContent {
		$this->option = $option;

		return $this;
	}


	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			'word'   => $this->getWord(),
			'should' => $this->getShould(),
			'match'  => $this->getMatch(),
			'option' => $this->getOption()
		];
	}

}

