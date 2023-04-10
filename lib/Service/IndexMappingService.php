<?php

declare(strict_types=1);

/**
 * FullTextSearch_Elasticsearch - Use Elasticsearch to index the content of your nextcloud
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

namespace OCA\FullTextSearch_Elasticsearch\Service;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\AccessIsEmptyException;
use OCA\FullTextSearch_Elasticsearch\Exceptions\ConfigurationException;
use OCP\FullTextSearch\Model\IIndexDocument;


/**
 * Class IndexMappingService
 *
 * @package OCA\FullTextSearch_Elasticsearch\Service
 */
class IndexMappingService {

	public function __construct(
		private ConfigService $configService
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
			'source' => $document->getSource(),
			'title' => $document->getTitle(),
			'parts' => $document->getParts()
		];
//		}

		$body['content'] = $document->getContent();

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

		$params['include_type_name'] = true;
		$params['body'] = [
			'settings' => [
				'index.mapping.total_fields.limit' => $this->configService->getAppValue(
					ConfigService::FIELDS_LIMIT
				),
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
							'tokenizer' => $this->configService->getAppValue(
								ConfigService::ANALYZER_TOKENIZER
							),
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

