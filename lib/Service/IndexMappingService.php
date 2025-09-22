<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Service;

use OCA\FullTextSearch_Elasticsearch\ConfigLexicon;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Client;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\ClientResponseException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\MissingParameterException;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Exception\ServerResponseException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\AccessIsEmptyException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\ConfigurationException;
use OCP\AppFramework\Services\IAppConfig;
use OCP\FullTextSearch\Model\IIndexDocument;


/**
 * Class IndexMappingService
 *
 * @package OCA\FullTextSearch_Elasticsearch\Service
 */
class IndexMappingService {

	public function __construct(
		private ConfigService $configService,
		private readonly IAppConfig $appConfig,
	) {
	}


	/**
	 * @param Client $client
	 * @param IIndexDocument $document
	 *
	 * @return array
	 * @throws AccessIsEmptyException
	 * @throws ConfigurationException
	 * @throws ClientResponseException
	 * @throws MissingParameterException
	 * @throws ServerResponseException
	 */
	public function indexDocumentNew(Client $client, IIndexDocument $document): array {
		$index = [
			'index' =>
				[
					'index' => $this->configService->getElasticIndex(),
					'id' => $document->getProviderId() . ':' . $document->getId(),
					'body' => $this->generateIndexBody($document)
				]
		];

		$this->onIndexingDocument($document, $index);
		$result = $client->index($index['index']);

		return $result->asArray();
	}


	/**
	 * @param Client $client
	 * @param IIndexDocument $document
	 *
	 * @return array
	 * @throws AccessIsEmptyException
	 * @throws ClientResponseException
	 * @throws ConfigurationException
	 * @throws MissingParameterException
	 * @throws ServerResponseException
	 */
	public function indexDocumentUpdate(Client $client, IIndexDocument $document): array {
		$index = [
			'index' =>
				[
					'index' => $this->configService->getElasticIndex(),
					'id' => $document->getProviderId() . ':' . $document->getId(),
					'body' => ['doc' => $this->generateIndexBody($document)]
				]
		];

		$this->onIndexingDocument($document, $index);
		try {
			$result = $client->update($index['index']);

			return $result->asArray();
		} catch (ClientResponseException $e) {
			return $this->indexDocumentNew($client, $document);
		}
	}


	/**
	 * @param Client $client
	 * @param string $providerId
	 * @param string $documentId
	 *
	 * @throws ConfigurationException
	 * @throws MissingParameterException
	 * @throws ServerResponseException
	 */
	public function indexDocumentRemove(Client $client, string $providerId, string $documentId): void {
		$index = [
			'index' =>
				[
					'index' => $this->configService->getElasticIndex(),
					'id' => $providerId . ':' . $documentId,
				]
		];

		try {
			$client->delete($index['index']);
		} catch (ClientResponseException $e) {
		}
	}


	/**
	 * @param IIndexDocument $document
	 * @param array $arr
	 */
	public function onIndexingDocument(IIndexDocument $document, array &$arr): void {
		if ($document->getContent() !== ''
			&& $document->isContentEncoded() === IIndexDocument::ENCODED_BASE64) {
			$arr['index']['pipeline'] = 'attachment';
		}
	}


	/**
	 * @param IIndexDocument $document
	 *
	 * @return array
	 * @throws AccessIsEmptyException
	 */
	public function generateIndexBody(IIndexDocument $document): array {
		$access = $document->getAccess();

		// TODO: check if we can just update META or just update CONTENT.
//		$index = $document->getIndex();
//		$body = [];

		// TODO: isStatus ALL or META (uncomment condition)
//		if ($index->isStatus(IIndex::INDEX_META)) {
		$body = [
			'owner' => $access->getOwnerId(),
			'users' => $access->getUsers(),
			'groups' => $access->getGroups(),
			'circles' => $access->getCircles(),
			'links' => $access->getLinks(),
			'metatags' => $document->getMetaTags(),
			'subtags' => $document->getSubTags(true),
			'tags' => $document->getTags(),
			'hash' => $document->getHash(),
			'provider' => $document->getProviderId(),
			'lastModified' => $document->getModifiedTime(),
			'source' => $document->getSource(),
			'title' => $document->getTitle(),
			'parts' => $document->getParts()
		];
//		}

		// TODO: isStatus ALL or CONTENT (uncomment condition)
//		if ($index->isStatus(IIndex::INDEX_CONTENT)) {
			$body['content'] = $document->getContent();
//		}
		return array_merge($document->getInfoAll(), $body);
	}


	/**
	 * @param bool $complete
	 *
	 * @return array
	 * @throws ConfigurationException
	 */
	public function generateGlobalMap(bool $complete = true): array {
		$params = [
			'index' => $this->configService->getElasticIndex()
		];

		if ($complete === false) {
			return $params;
		}

		$params['body'] = [
			'settings' => [
				'index.mapping.total_fields.limit' => $this->appConfig->getAppValueInt(ConfigLexicon::FIELDS_LIMIT),
				'analysis' => [
					'filter' => [
						'shingle' => [
							'type' => 'shingle'
						]
					],
					'char_filter' => [
						'pre_negs' => [
							'type' => 'pattern_replace',
							'pattern' => '(\\w+)\\s+((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\b',
							'replacement' => '~$1 $2'
						],
						'post_negs' => [
							'type' => 'pattern_replace',
							'pattern' => '\\b((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\s+(\\w+)',
							'replacement' => '$1 ~$2'
						]
					],
					'analyzer' => [
						'analyzer' => [
							'type' => 'custom',
							'tokenizer' => $this->appConfig->getAppValueString(ConfigLexicon::ANALYZER_TOKENIZER),
							'filter' => ['lowercase', 'stop', 'kstem']
						]
					]
				]
			],
			'mappings' => [
				'standard' => [
					'dynamic' => true,
					'properties' => [
						'source' => [
							'type' => 'keyword'
						],
						'title' => [
							'type' => 'text',
							'analyzer' => 'keyword',
							'term_vector' => 'with_positions_offsets',
							'copy_to' => 'combined'
						],
						'provider' => [
							'type' => 'keyword'
						],
						'lastModified' => [
							'type' => 'integer',
						],
						'tags' => [
							'type' => 'keyword'
						],
						'metatags' => [
							'type' => 'keyword'
						],
						'subtags' => [
							'type' => 'keyword'
						],
						'content' => [
							'type' => 'text',
							'analyzer' => 'analyzer',
							'term_vector' => 'with_positions_offsets',
							'copy_to' => 'combined'
						],
						'owner' => [
							'type' => 'keyword'
						],
						'users' => [
							'type' => 'keyword'
						],
						'groups' => [
							'type' => 'keyword'
						],
						'circles' => [
							'type' => 'keyword'
						],
						'links' => [
							'type' => 'keyword'
						],
						'hash' => [
							'type' => 'keyword'
						],
						'combined' => [
							'type' => 'text',
							'analyzer' => 'analyzer',
							'term_vector' => 'with_positions_offsets'
						]
					]
				]
			]
		];

		return $params;
	}


	/**
	 * @param bool $complete
	 *
	 * @return array
	 */
	public function generateGlobalIngest(bool $complete = true): array {
		$params = ['id' => 'attachment'];

		if ($complete === false) {
			return $params;
		}

		$params['body'] = [
			'description' => 'attachment',
			'processors' => [
				[
					'attachment' => [
						'field' => 'content',
						'indexed_chars' => -1
					],
					'convert' => [
						'field' => 'attachment.content',
						'type' => 'string',
						'target_field' => 'content',
						'ignore_failure' => true
					],
					'remove' => [
						'field' => 'attachment.content',
						'ignore_failure' => true
					]
				]
			]
		];

		return $params;
	}


	/**
	 * @param string $providerId
	 *
	 * @return array
	 * @throws ConfigurationException
	 */
	public function generateDeleteQuery(string $providerId): array {
		$params = [
			'index' => $this->configService->getElasticIndex()
		];

		$params['body']['query']['match'] = ['provider' => $providerId];

		return $params;
	}
}

