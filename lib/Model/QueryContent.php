<?php
declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Model;


use JsonSerializable;


/**
 * Class QueryContent
 *
 * @package OCA\FullTextSearch_Elasticsearch\Model
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

