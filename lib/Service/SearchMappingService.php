<?php
declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Service;


use OCA\FullTextSearch_Elasticsearch\Exceptions\ConfigurationException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\QueryContentGenerationException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\SearchQueryGenerationException;
use OCA\FullTextSearch_Elasticsearch\Model\QueryContent;
use OCP\FullTextSearch\Model\IDocumentAccess;
use OCP\FullTextSearch\Model\ISearchRequest;
use OCP\FullTextSearch\Model\ISearchRequestSimpleQuery;
use stdClass;


/**
 * Class SearchMappingService
 *
 * @package OCA\FullTextSearch_Elasticsearch\Service
 */
class SearchMappingService {

	public function __construct
	(
		private ConfigService $configService
	) {
	}


	/**
	 * @param ISearchRequest $request
	 * @param IDocumentAccess $access
	 * @param string $providerId
	 *
	 * @return array
	 * @throws ConfigurationException
	 * @throws SearchQueryGenerationException
	 */
	public function generateSearchQuery(
		ISearchRequest $request,
		IDocumentAccess $access,
		string $providerId
	): array {
		$query['params'] = $this->generateSearchQueryParams($request, $access, $providerId);

		return $query;
	}


	/**
	 * @param ISearchRequest $request
	 * @param IDocumentAccess $access
	 * @param string $providerId
	 *
	 * @return array
	 * @throws ConfigurationException
	 * @throws SearchQueryGenerationException
	 */
	public function generateSearchQueryParams(
		ISearchRequest $request,
		IDocumentAccess $access,
		string $providerId
	): array {
		$params = [
			'index' => $this->configService->getElasticIndex(),
			'size' => $request->getSize(),
			'from' => (($request->getPage() - 1) * $request->getSize()),
			'_source_excludes' => 'content'
		];

		$bool = [];
		if ($request->getSearch() !== '') {
			$bool['must']['bool'] = $this->generateSearchQueryContent($request);
		}

		$bool['filter'][]['bool']['must'] = ['term' => ['provider' => $providerId]];
		$bool['filter'][]['bool']['should'] = $this->generateSearchQueryAccess($access);
		$bool['filter'][]['bool']['should'] =
			$this->generateSearchQueryTags('metatags', $request->getMetaTags());

		$bool['filter'][]['bool']['must'] =
			$this->generateSearchQueryTags('subtags', $request->getSubTags(true));

		$bool['filter'][]['bool']['must'] =
			$this->generateSearchSimpleQuery($request->getSimpleQueries());

//		$bool['filter'][]['bool']['should'] = $this->generateSearchQueryTags($request->getTags());

		$this->generateSearchSince($bool, (int)$request->getOption('since'));

		$params['body']['query']['bool'] = $bool;
		$params['body']['highlight'] = $this->generateSearchHighlighting($request);

		$this->improveSearchQuerying($request, $params['body']['query']);

		return $params;
	}


	/**
	 * @param ISearchRequest $request
	 * @param array $arr
	 */
	private function improveSearchQuerying(ISearchRequest $request, array &$arr): void {
//		$this->improveSearchWildcardQueries($request, $arr);
		$this->improveSearchWildcardFilters($request, $arr);
		$this->improveSearchRegexFilters($request, $arr);
	}


//	/**
//	 * @param SearchRequest $request
//	 * @param array $arr
//	 */
//	private function improveSearchWildcardQueries(SearchRequest $request, &$arr) {
//
//		$queries = $request->getWildcardQueries();
//		foreach ($queries as $query) {
//			$wildcards = [];
//			foreach ($query as $entry) {
//				$wildcards[] = ['wildcard' => $entry];
//			}
//
//			array_push($arr['bool']['must']['bool']['should'], $wildcards);
//		}
//
//	}


	/**
	 * @param ISearchRequest $request
	 * @param array $arr
	 */
	private function improveSearchWildcardFilters(ISearchRequest $request, array &$arr): void {
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
	 * @param ISearchRequest $request
	 * @param array $arr
	 */
	private function improveSearchRegexFilters(ISearchRequest $request, array &$arr): void {
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
	 * @param ISearchRequest $request
	 *
	 * @return array
	 * @throws SearchQueryGenerationException
	 */
	private function generateSearchQueryContent(ISearchRequest $request): array {
		$str = strtolower($request->getSearch());

		preg_match_all('/[^?]"(?:\\\\.|[^\\\\"])*"|\S+/', " $str ", $words);
		$queryContent = [];
		foreach ($words[0] as $word) {
			try {
				$queryContent[] = $this->generateQueryContent(trim($word));
			} catch (QueryContentGenerationException $e) {
				continue;
			}
		}

		if (sizeof($queryContent) === 0) {
			throw new SearchQueryGenerationException();
		}

		return $this->generateSearchQueryFromQueryContent($request, $queryContent);
	}


	/**
	 * @param string $word
	 *
	 * @return QueryContent
	 * @throws QueryContentGenerationException
	 */
	private function generateQueryContent(string $word): QueryContent {
		$searchQueryContent = new QueryContent($word);
		if (strlen($searchQueryContent->getWord()) === 0) {
			throw new QueryContentGenerationException();
		}

		return $searchQueryContent;
	}


	/**
	 * @param ISearchRequest $request
	 * @param QueryContent[] $contents
	 *
	 * @return array
	 */
	private function generateSearchQueryFromQueryContent(ISearchRequest $request, array $contents): array {
		$query = [];
		foreach ($contents as $content) {
			if (!array_key_exists($content->getShould(), $query)) {
				$query[$content->getShould()] = [];
			}

			if ($content->getShould() === 'must') {
				$query[$content->getShould()][] =
					['bool' => ['should' => $this->generateQueryContentFields($request, $content)]];
			} else {
				$query[$content->getShould()] = array_merge(
					$query[$content->getShould()], $this->generateQueryContentFields($request, $content)
				);
			}
		}

		return $query;
	}


	/**
	 * @param ISearchRequest $request
	 * @param QueryContent $content
	 *
	 * @return array
	 */
	private function generateQueryContentFields(ISearchRequest $request, QueryContent $content): array {
		$queryFields = [];

		$fields = array_merge(['content', 'title'], $request->getFields());
		foreach ($fields as $field) {
			if (!$this->fieldIsOutLimit($request, $field)) {
				$queryFields[] = [$content->getMatch() => [$field => $content->getWord()]];
			}
		}

		foreach ($request->getWildcardFields() as $field) {
			if (!$this->fieldIsOutLimit($request, $field)) {
				$queryFields[] = ['wildcard' => [$field => '*' . $content->getWord() . '*']];
			}
		}

		$parts = [];
		foreach ($this->getPartsFields($request) as $field) {
			if (!$this->fieldIsOutLimit($request, $field)) {
				$parts[] = $field;
			}
		}

		if (sizeof($parts) > 0) {
			$queryFields[] = [
				'query_string' => [
					'fields' => $parts,
					'query' => $content->getWord()
				]
			];
		}

		return $queryFields;
	}


	/**
	 * @param IDocumentAccess $access
	 *
	 * @return array
	 */
	private function generateSearchQueryAccess(IDocumentAccess $access): array {
		$query = [];
		$query[] = ['term' => ['owner.keyword' => $access->getViewerId()]];
		$query[] = ['term' => ['users.keyword' => $access->getViewerId()]];
		$query[] = ['term' => ['users.keyword' => '__all']];

		foreach ($access->getGroups() as $group) {
			$query[] = ['term' => ['groups.keyword' => $group]];
		}

		foreach ($access->getCircles() as $circle) {
			$query[] = ['term' => ['circles.keyword' => $circle]];
		}

		return $query;
	}


	/**
	 * @param ISearchRequest $request
	 * @param string $field
	 *
	 * @return bool
	 */
	private function fieldIsOutLimit(ISearchRequest $request, string $field): bool {
		$limit = $request->getLimitFields();
		if (sizeof($limit) === 0) {
			return false;
		}

		if (in_array($field, $limit)) {
			return false;
		}

		return true;
	}


	/**
	 * @param string $k
	 * @param array $tags
	 *
	 * @return array
	 */
	private function generateSearchQueryTags(string $k, array $tags): array {
		$query = [];
		foreach ($tags as $t) {
			$query[] = ['term' => [$k => $t]];
		}

		return $query;
	}

	private function generateSearchSince(array &$bool, int $since): void {
		if ($since === 0) {
			return;
		}

		$query = [];
		$query[] = ['range' => ['lastModified' => ['gte' => $since]]];

		$bool['filter'][]['bool']['must'] = $query;
	}


	/**
	 * @param ISearchRequestSimpleQuery[] $queries
	 *
	 * @return array
	 */
	private function generateSearchSimpleQuery(array $queries): array {
		$simpleQuery = [];
		foreach ($queries as $query) {
			// TODO: manage multiple entries array

			if ($query->getType() === ISearchRequestSimpleQuery::COMPARE_TYPE_KEYWORD) {
				$value = $query->getValues()[0];
				$simpleQuery[] = ['term' => [$query->getField() => $value]];
			}

			if ($query->getType() === ISearchRequestSimpleQuery::COMPARE_TYPE_WILDCARD) {
				$value = $query->getValues()[0];
				$simpleQuery[] = ['wildcard' => [$query->getField() => $value]];
			}

			if ($query->getType() === ISearchRequestSimpleQuery::COMPARE_TYPE_INT_EQ) {
				$value = $query->getValues()[0];
				$simpleQuery[] = ['term' => [$query->getField() => $value]];
			}

			if ($query->getType() === ISearchRequestSimpleQuery::COMPARE_TYPE_INT_GTE) {
				$value = $query->getValues()[0];
				$simpleQuery[] = ['range' => [$query->getField() => ['gte' => $value]]];
			}

			if ($query->getType() === ISearchRequestSimpleQuery::COMPARE_TYPE_INT_LTE) {
				$value = $query->getValues()[0];
				$simpleQuery[] = ['range' => [$query->getField() => ['lte' => $value]]];
			}

			if ($query->getType() === ISearchRequestSimpleQuery::COMPARE_TYPE_INT_GT) {
				$value = $query->getValues()[0];
				$simpleQuery[] = ['range' => [$query->getField() => ['gt' => $value]]];
			}

			if ($query->getType() === ISearchRequestSimpleQuery::COMPARE_TYPE_INT_LT) {
				$value = $query->getValues()[0];
				$simpleQuery[] = ['range' => [$query->getField() => ['lt' => $value]]];
			}

		}

		return $simpleQuery;
	}


	/**
	 * @param ISearchRequest $request
	 *
	 * @return array
	 */
	private function generateSearchHighlighting(ISearchRequest $request): array {
		$parts = $this->getPartsFields($request);
		$fields = ['content' => new stdClass()];
		foreach ($parts as $part) {
			$fields[$part] = new stdClass();
		}

		return [
			'fields' => $fields,
			'pre_tags' => [''],
			'post_tags' => [''],
			'max_analyzed_offset' => 1000000,
		];
	}


	/**
	 * @param string $providerId
	 * @param string $documentId
	 *
	 * @return array
	 * @throws ConfigurationException
	 */
	public function getDocumentQuery(string $providerId, string $documentId): array {
		return [
			'index' => $this->configService->getElasticIndex(),
			'id' => $providerId . ':' . $documentId
		];
	}


	/**
	 * @param ISearchRequest $request
	 *
	 * @return array
	 */
	private function getPartsFields(ISearchRequest $request): array {
		return array_map(
			function (string $value): string {
				return 'parts.' . $value;
			}, $request->getParts()
		);
	}
}

