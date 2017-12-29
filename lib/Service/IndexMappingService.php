<?php
/**
 * FullNextSearch_ElasticSearch - Index with ElasticSearch
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2017
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

namespace OCA\FullNextSearch_ElasticSearch\Service;

use Elasticsearch\Client;
use OCA\FullNextSearch\INextSearchPlatform;
use OCA\FullNextSearch\INextSearchProvider;
use OCA\FullNextSearch\Model\IndexDocument;
use OCA\FullNextSearch_ElasticSearch\Exceptions\ConfigurationException;


class IndexMappingService {

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
	 * @param Client $client
	 * @param INextSearchProvider $provider
	 * @param IndexDocument $document
	 *
	 * @param INextSearchPlatform $source
	 *
	 * @return array
	 * @throws ConfigurationException
	 */
	public function indexDocumentNew(
		Client $client, INextSearchProvider $provider, IndexDocument $document,
		INextSearchPlatform $source
	) {
		$index = [
			'index' =>
				[
					'index' => $this->configService->getElasticIndex(),
					'id'    => $document->getId(),
					'type'  => $provider->getId(),
					'body'  => $this->generateIndexBody($document)
				]
		];

		$this->onIndexingDocument($source, $provider, $document, $index);

		return $client->index($index['index']);
	}


	/**
	 * @param Client $client
	 * @param INextSearchProvider $provider
	 * @param IndexDocument $document
	 * @param INextSearchPlatform $source
	 *
	 * @return array
	 * @throws ConfigurationException
	 */
	public function indexDocumentUpdate(
		Client $client, INextSearchProvider $provider, IndexDocument $document,
		INextSearchPlatform $source
	) {
		$index = [
			'index' =>
				[
					'index' => $this->configService->getElasticIndex(),
					'id'    => $document->getId(),
					'type'  => $provider->getId(),
					'body'  => ['doc' => $this->generateIndexBody($document)]
				]
		];

		$this->onIndexingDocument($source, $provider, $document, $index);

		return $client->update($index['index']);
	}


	/**
	 * @param Client $client
	 * @param INextSearchProvider $provider
	 * @param IndexDocument $document
	 *
	 * @return array
	 * @throws ConfigurationException
	 */
	public function indexDocumentRemove(
		Client $client, INextSearchProvider $provider, IndexDocument $document
	) {
		$index = [
			'index' =>
				[
					'index' => $this->configService->getElasticIndex(),
					'id'    => $document->getId(),
					'type'  => $provider->getId()
				]
		];

		return $client->delete($index['index']);
	}


	/**
	 * @param INextSearchPlatform $source
	 * @param INextSearchProvider $provider
	 * @param IndexDocument $document
	 * @param array $arr
	 */
	public function onIndexingDocument(
		INextSearchPlatform $source, INextSearchProvider $provider, IndexDocument $document, &$arr
	) {
		if ($document->isContentEncoded() === IndexDocument::ENCODED_BASE64) {
			$arr['index']['pipeline'] = 'attachment';
		}

		$provider->onIndexingDocument($source, $arr);
	}


	/**
	 * @param IndexDocument $document
	 *
	 * @return array
	 */
	public function generateIndexBody(IndexDocument $document) {

		$body = [];
		$access = $document->getAccess();
		if ($access !== null) {
			$body = [
				'owner'   => $access->getOwnerId(),
				'users'   => $access->getUsers(),
				'groups'  => $access->getGroups(),
				'circles' => $access->getCircles()
			];
		}

		$body['tags'] = $document->getTags();

		if ($document->getTitle() !== null) {
			$body['title'] = $document->getTitle();
		}

		if ($document->getContent() !== null) {
			$body['content'] = $document->getContent();
		}

		return array_merge($document->getInfoAll(), $body);
	}


	/**
	 * @param bool $complete
	 *
	 * @return array<string,string|array<string,array<string,array<string,array>>>>
	 * @throws ConfigurationException
	 */
	public function generateGlobalMap($complete = true) {

		$params = [
			'index' => $this->configService->getElasticIndex()
		];

		if ($complete === false) {
			return $params;
		}

		$params['body'] = [
			'settings' => [
				'analysis' => [
					'filter'      => [
						'shingle' => [
							'type' => 'shingle'
						]
					],
					'char_filter' => [
						'pre_negs'  => [
							'type'        => 'pattern_replace',
							'pattern'     => '(\\w+)\\s+((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\b',
							'replacement' => '~$1 $2'
						],
						'post_negs' => [
							'type'        => 'pattern_replace',
							'pattern'     => '\\b((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\s+(\\w+)',
							'replacement' => '$1 ~$2'
						]
					],
					'analyzer'    => [
						'analyzer' => [
							'type'      => 'custom',
							'tokenizer' => 'standard',
							'filter'    => ['lowercase', 'stop', 'kstem']
						]
					]
				]
			],
			'mappings' => [
				'_default_' => [
					'properties' => [
						'title'    => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'content'  => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'owner'    => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'users'    => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'groups'   => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'circles'  => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'combined' => [
							'type'        => 'text',
							'analyzer'    => 'analyzer',
							'term_vector' => 'yes'
						]
						//						,
						//						'topics'   => [
						//							'type'  => 'text',
						//							'index' => 'not_analyzed'
						//						],
						//						'places'   => [
						//							'type'  => 'text',
						//							'index' => 'not_analyzed'
						//						]
					]
				]
			]
		];

		return $params;
	}


	/**
	 * @param bool $complete
	 *
	 * @return array<string,string|array<string,string|array<string,array<string,string|integer>>>>
	 */
	public function generateGlobalIngest($complete = true) {

		$params = ['id' => 'attachment'];

		if ($complete === false) {
			return $params;
		}

		$params['body'] = [
			'description' => 'attachment',
			'processors'  => [
				[
					'attachment' => [
						'field'         => 'content',
						'indexed_chars' => -1
					],
					'set'        => [
						'field' => 'content',
						'value' => '{{ attachment.content }}'
					],
					'remove'     => [
						'field'          => 'attachment.content',
						'ignore_failure' => true
					]
				]
			]
		];

		return $params;
	}

}
