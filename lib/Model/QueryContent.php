<?php


namespace OCA\FullTextSearch_ElasticSearch\Model;


class QueryContent {


	const OPTION_MUST = 1;
	const OPTION_MUST_NOT = 2;


	/** @var string */
	private $word;

	/** @var string */
	private $should;

	/** @var string */
	private $match;

	/** @var int */
	private $option;


	/** @var array */
	private $options = [
               '+' => [self::OPTION_MUST, 'must', 'match'],
               '-' => [self::OPTION_MUST_NOT, 'must_not', 'match']
	];


	/**
	 * SearchQueryContent constructor.
	 *
	 * @param string $word
	 */
	function __construct($word) {
		$this->word = $word;

		$this->init();
	}


	private function init() {
		$this->setShould('should');
               $this->setMatch('match');

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
                               $this->setMatch('match');
			}
		}

		$this->setWord(str_replace('"', '', $this->getWord()));
	}


	/**
	 * @return string
	 */
	public function getWord() {
		return $this->word;
	}

	/**
	 * @param string $word
	 */
	public function setWord($word) {
		$this->word = $word;
	}


	/**
	 * @return string
	 */
	public function getShould() {
		return $this->should;
	}

	/**
	 * @param string $should
	 *
	 * @return QueryContent
	 */
	public function setShould($should) {
		$this->should = $should;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getMatch() {
		return $this->match;
	}

	/**
	 * @param string $match
	 *
	 * @return QueryContent
	 */
	public function setMatch($match) {
		$this->match = $match;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getOption() {
		return $this->option;
	}

	/**
	 * @param int $option
	 *
	 * @return QueryContent
	 */
	public function setOption($option) {
		$this->option = $option;

		return $this;
	}


}
