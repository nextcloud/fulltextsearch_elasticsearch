<?php
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

namespace OCA\FullTextSearch_ElasticSearch\Service;

use OCA\FullTextSearch\IFullTextSearchProvider;
use OCA\FullTextSearch\Model\DocumentAccess;
use OCA\FullTextSearch\Model\SearchRequest;
use OCA\FullTextSearch_ElasticSearch\Exceptions\ConfigurationException;
use OCA\FullTextSearch_ElasticSearch\Exceptions\SearchQueryGenerationException;


class SearchMappingService {

	/** @var ConfigService */
	private $configService;

	/** @var MiscService */
	private $miscService;


	/**
	 * MappingService constructor.
	 *
	 * @param ConfigService $configService
	 * @param MiscService $miscService
	 */
	public function __construct(ConfigService $configService, MiscService $miscService) {
		$this->configService = $configService;
		$this->miscService = $miscService;
	}


	/**
	 * @param IFullTextSearchProvider $provider
	 * @param DocumentAccess $access
	 * @param SearchRequest $request
	 *
	 * @return array
	 * @throws ConfigurationException
	 * @throws SearchQueryGenerationException
	 */
	public function generateSearchQuery(
		IFullTextSearchProvider $provider, DocumentAccess $access, SearchRequest $request
	) {
		$query['params'] = $this->generateSearchQueryParams($provider, $access, $request);

		return $query;
	}


	/**
	 * @param IFullTextSearchProvider $provider
	 * @param DocumentAccess $access
	 * @param SearchRequest $request
	 *
	 * @return array
	 * @throws ConfigurationException
	 * @throws SearchQueryGenerationException
	 */
	public function generateSearchQueryParams(
		IFullTextSearchProvider $provider, DocumentAccess $access, SearchRequest $request
	) {
		$params = [
			'index' => $this->configService->getElasticIndex(),
			'type'  => 'standard',
			'size'  => $request->getSize(),
			'from'  => (($request->getPage() - 1) * $request->getSize())
		];

		$bool = [];
		$bool['must']['bool']['should'] = $this->generateSearchQueryContent($request);

		$bool['filter'][]['bool']['must'] = ['term' => ['provider' => $provider->getId()]];
		$bool['filter'][]['bool']['should'] = $this->generateSearchQueryAccess($access);
		$bool['filter'][]['bool']['should'] = $this->generateSearchQueryTags($request->getTags());

		$params['body']['query']['bool'] = $bool;
		$params['body']['highlight'] = $this->generateSearchHighlighting();

		$this->improveSearchQuerying($request, $params['body']['query']);

		return $params;
	}


	/**
	 * @param SearchRequest $request
	 * @param array $arr
	 */
	private function improveSearchQuerying(SearchRequest $request, &$arr) {
		$this->improveSearchWildcardQueries($request, $arr);
		$this->improveSearchWildcardFilters($request, $arr);
		$this->improveSearchRegexFilters($request, $arr);
	}


	/**
	 * @param SearchRequest $request
	 * @param array $arr
	 */
	private function improveSearchWildcardQueries(SearchRequest $request, &$arr) {

		$queries = $request->getWildcardQueries();
		foreach ($queries as $query) {
			$wildcards = [];
			foreach ($query as $entry) {
				$wildcards[] = ['wildcard' => $entry];
			}

			array_push($arr['bool']['must']['bool']['should'], $wildcards);
		}

	}


	/**
	 * @param SearchRequest $request
	 * @param array $arr
	 */
	private function improveSearchWildcardFilters(SearchRequest $request, &$arr) {

		$filters = $request->getWildcardFilters();
		foreach ($filters as $filter) {
			$wildcards = [];
			foreach ($filter as $entry) {
				$wildcards[] = ['wildcard' => $entry];
			}

			$arr['bool']['filter'][]['bool']['should'] = $wildcards;
		}

	}


	/**
	 * @param SearchRequest $request
	 * @param array $arr
	 */
	private function improveSearchRegexFilters(SearchRequest $request, &$arr) {

		$filters = $request->getRegexFilters();
		foreach ($filters as $filter) {
			$regex = [];
			foreach ($filter as $entry) {
				$regex[] = ['regexp' => $entry];
			}

			$arr['bool']['filter'][]['bool']['should'] = $regex;
		}

	}


	/**
	 * @param SearchRequest $request
	 *
	 * @return array<string,array<string,array>>
	 * @throws SearchQueryGenerationException
	 */
	private function generateSearchQueryContent(SearchRequest $request) {
		$str = strtolower($request->getSearch());

		preg_match_all('/[^?]"(?:\\\\.|[^\\\\"])*"|\S+/', " $str ", $words);
		$queryData = [];
		foreach ($words[0] as $word) {
			list($bool, $should, $match) = $this->generateSearchQueryOption($word);
			if (strlen($word) === 0) {
				continue;
			}

			$queryData[] = [
				'bool'   => $bool,
				'should' => $should,
				'match'  => $match,
				'word'   => $word
			];
		}

		if (sizeof($queryData) === 0) {
			throw new SearchQueryGenerationException();
		}

		$query = $this->generateSearchQueryContentFromOptions($request, $queryData);

//		$query = array_merge($query, $this->complementSearchWithParts($request, $kwParts));

		return $query;
	}


	/**
	 * @param string $word
	 *
	 * @return array
	 */
	private function generateSearchQueryOption(&$word) {
		$bool = 'bool';
		$should = 'should';
		$match = 'match_phrase_prefix';

		$curr = substr($word, 0, 1);
		$options = [
			'+' => ['bool', 'must', 'prefix'],
			'-' => ['bool', 'must_not', 'prefix']
		];

		if (array_key_exists($curr, $options)) {
			$bool = $options[$curr][0];
			$should = $options[$curr][1];
			$match = $options[$curr][2];
			$word = substr($word, 1);
		}

		if (substr($word, 0, 1) === '"') {
			$match = 'match';
			if (strpos($word, " ") > -1) {
				$match = 'match_phrase_prefix';
			}
		}

		$word = str_replace('"', '', $word);

		return [$bool, $should, $match];
	}


	/**
	 * @param SearchRequest $request
	 * @param array $kwParts
	 *
	 * @return array
	 */
	private function complementSearchWithParts(SearchRequest $request, $kwParts) {
		$query = [];
		foreach ($request->getParts() as $part) {
			$queryParts = [];

			foreach ($kwParts as $kwPart) {
				$queryParts[] = [$kwPart['kw'] => ['parts.' . $part => $kwPart['word']]];
			}

			$query[] = ['bool' => ['must' => $queryParts]];
		}

		return $query;
	}


	/**
	 * @param SearchRequest $request
	 * @param array $queryData
	 *
	 * @return array
	 */
	private function generateSearchQueryContentFromOptions(SearchRequest $request, $queryData) {
		$query = [];
		$fields = array_merge(['content', 'title'], $request->getParts());
		foreach ($fields as $field) {
			$queryField = [];
			foreach ($queryData as $data) {
				$bool = $data['bool'];
				$should = $data['should'];
				$match = $data['match'];
				$word = $data['word'];

				$queryField[$bool][$should][] = [$match => [$field => $word]];
			}

			$query[] = $queryField;
		}

		return $query;
	}


	/**
	 * @param DocumentAccess $access
	 *
	 * @return array<string,array>
	 */
	private function generateSearchQueryAccess(DocumentAccess $access) {

		$query = [];
		$query[] = ['term' => ['owner' => $access->getViewerId()]];
		$query[] = ['term' => ['users' => $access->getViewerId()]];
		$query[] = ['term' => ['users' => '__all']];

		foreach ($access->getGroups() as $group) {
			$query[] = ['term' => ['groups' => $group]];
		}

		foreach ($access->getCircles() as $circle) {
			$query[] = ['term' => ['circles' => $circle]];
		}

		return $query;
	}


	/**
	 * @param array $tags
	 *
	 * @return array<string,array>
	 */
	private function generateSearchQueryTags($tags) {

		$query = [];
		foreach ($tags as $tag) {
			$query[] = ['term' => ['tags' => $tag]];
		}

		return $query;
	}

	private function generateSearchHighlighting() {
		return [
			'fields'    => ['content' => new \stdClass()],
			'pre_tags'  => [''],
			'post_tags' => ['']
		];
	}


}
