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


namespace OCA\FullTextSearch_OpenSearch\Model;

use JsonSerializable;

/**
 * Class QueryContent
 *
 * @package OCA\FullTextSearch_OpenSearch\Model
 */
class QueryContent implements JsonSerializable {


	public const OPTION_MUST = 1;
	public const OPTION_MUST_NOT = 2;


	/** @var string */
	private string $word;

	/** @var string */
	private string $should;

	/** @var string */
	private string $match;

	/** @var int */
	private int $option = 0;


	/** @var array */
	private array $options = [
		'+' => [self::OPTION_MUST, 'must', 'match_phrase_prefix'],
		'-' => [self::OPTION_MUST_NOT, 'must_not', 'match_phrase_prefix']
	];


    /**
     * Constructor for initializing the object with a given word and performing initialization tasks.
     *
     * @param string $word The word to initialize the object with.
     * @return void
     */
	public function __construct(string $word) {
		$this->word = $word;

		$this->init();
	}


    /**
     * Initializes the instance with default values and modifies them based on specific conditions.
     *
     * @return void
     */
	private function init(): void
    {
		$this->setShould('should');
		$this->setMatch('match_phrase_prefix');

		$curr = substr($this->getWord(), 0, 1);

		if (array_key_exists($curr, $this->options)) {
			$this->setOption($this->options[$curr][0])
				->setShould($this->options[$curr][1])
				->setMatch($this->options[$curr][2])
				->setWord(substr($this->getWord(), 1));
		}

		if (str_starts_with($this->getWord(), '"')) {
			$this->setMatch('match');
			if (strpos($this->getWord(), ' ') > -1) {
				$this->setMatch('match_phrase_prefix');
			}
		}

		$this->setWord(str_replace('"', '', $this->getWord()));
	}


    /**
     * @return string
     */
	final public function getWord(): string {
		return $this->word;
	}

    /**
     * Sets the word for the query content.
     *
     * @param string $word The word to set.
     * @return QueryContent
     */
	final public function setWord(string $word): QueryContent {
		$this->word = $word;

		return $this;
	}


	/**
	 * @return string
	 */
	final public function getShould(): string {
		return $this->should;
	}

    /**
     * Sets the value of the should property.
     *
     * @param string $should The value to set for the should property.
     * @return QueryContent Returns the current instance of QueryContent.
     */
	final public function setShould(string $should): QueryContent {
		$this->should = $should;

		return $this;
	}


    /**
     * @return string
     */
	final public function getMatch(): string {
		return $this->match;
	}

    /**
     * Sets the match string and returns the updated QueryContent object.
     *
     * @param string $match The match string to set.
     * @return QueryContent The current instance of the QueryContent object.
     */
	final public function setMatch(string $match): QueryContent {
		$this->match = $match;

		return $this;
	}


	/**
	 * @return int
	 */
	final public function getOption(): int {
		return $this->option;
	}

    /**
     * Sets the option value.
     *
     * @param int $option The option value to set.
     * @return QueryContent Returns the current instance for method chaining.
     */
	final public function setOption(int $option): QueryContent {
		$this->option = $option;

		return $this;
	}


    /**
     * @return array
     */
	final public function jsonSerialize(): array {
		return [
			'word' => $this->getWord(),
			'should' => $this->getShould(),
			'match' => $this->getMatch(),
			'option' => $this->getOption()
		];
	}

}
