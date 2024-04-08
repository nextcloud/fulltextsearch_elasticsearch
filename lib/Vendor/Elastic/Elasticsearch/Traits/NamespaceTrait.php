<?php

/**
 * Elasticsearch PHP Client
 *
 * @link      https://github.com/elastic/elasticsearch-php
 * @copyright Copyright (c) Elasticsearch B.V (https://www.elastic.co)
 * @license   https://opensource.org/licenses/MIT MIT License
 *
 * Licensed to Elasticsearch B.V under one or more agreements.
 * Elasticsearch B.V licenses this file to you under the MIT License.
 * See the LICENSE file in the project root for more information.
 */

declare(strict_types=1);

namespace OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Traits;

use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\AsyncSearch;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Autoscaling;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Cat;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Ccr;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Cluster;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Connector;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\ConnectorSyncJob;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\DanglingIndices;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Enrich;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Eql;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Esql;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Features;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Fleet;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Graph;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Ilm;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Indices;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Inference;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Ingest;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\License;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Logstash;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Migration;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Ml;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Monitoring;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Nodes;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Profiling;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\QueryRuleset;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Rollup;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\SearchApplication;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\SearchableSnapshots;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Security;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Shutdown;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Simulate;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Slm;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Snapshot;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Sql;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Ssl;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Synonyms;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Tasks;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\TextStructure;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Transform;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Watcher;
use OCA\FullTextSearch_Elasticsearch\Vendor\Elastic\Elasticsearch\Endpoints\Xpack;

/**
 * @generated This file is generated, please do not edit
 */
trait NamespaceTrait
{
	/** The endpoint namespace storage */
	protected array $namespace;


	public function asyncSearch(): AsyncSearch
	{
		if (!isset($this->namespace['AsyncSearch'])) {
			$this->namespace['AsyncSearch'] = new AsyncSearch($this);
		}
		return $this->namespace['AsyncSearch'];
	}


	public function autoscaling(): Autoscaling
	{
		if (!isset($this->namespace['Autoscaling'])) {
			$this->namespace['Autoscaling'] = new Autoscaling($this);
		}
		return $this->namespace['Autoscaling'];
	}


	public function cat(): Cat
	{
		if (!isset($this->namespace['Cat'])) {
			$this->namespace['Cat'] = new Cat($this);
		}
		return $this->namespace['Cat'];
	}


	public function ccr(): Ccr
	{
		if (!isset($this->namespace['Ccr'])) {
			$this->namespace['Ccr'] = new Ccr($this);
		}
		return $this->namespace['Ccr'];
	}


	public function cluster(): Cluster
	{
		if (!isset($this->namespace['Cluster'])) {
			$this->namespace['Cluster'] = new Cluster($this);
		}
		return $this->namespace['Cluster'];
	}


	public function connector(): Connector
	{
		if (!isset($this->namespace['Connector'])) {
			$this->namespace['Connector'] = new Connector($this);
		}
		return $this->namespace['Connector'];
	}


	public function connectorSyncJob(): ConnectorSyncJob
	{
		if (!isset($this->namespace['ConnectorSyncJob'])) {
			$this->namespace['ConnectorSyncJob'] = new ConnectorSyncJob($this);
		}
		return $this->namespace['ConnectorSyncJob'];
	}


	public function danglingIndices(): DanglingIndices
	{
		if (!isset($this->namespace['DanglingIndices'])) {
			$this->namespace['DanglingIndices'] = new DanglingIndices($this);
		}
		return $this->namespace['DanglingIndices'];
	}


	public function enrich(): Enrich
	{
		if (!isset($this->namespace['Enrich'])) {
			$this->namespace['Enrich'] = new Enrich($this);
		}
		return $this->namespace['Enrich'];
	}


	public function eql(): Eql
	{
		if (!isset($this->namespace['Eql'])) {
			$this->namespace['Eql'] = new Eql($this);
		}
		return $this->namespace['Eql'];
	}


	public function esql(): Esql
	{
		if (!isset($this->namespace['Esql'])) {
			$this->namespace['Esql'] = new Esql($this);
		}
		return $this->namespace['Esql'];
	}


	public function features(): Features
	{
		if (!isset($this->namespace['Features'])) {
			$this->namespace['Features'] = new Features($this);
		}
		return $this->namespace['Features'];
	}


	public function fleet(): Fleet
	{
		if (!isset($this->namespace['Fleet'])) {
			$this->namespace['Fleet'] = new Fleet($this);
		}
		return $this->namespace['Fleet'];
	}


	public function graph(): Graph
	{
		if (!isset($this->namespace['Graph'])) {
			$this->namespace['Graph'] = new Graph($this);
		}
		return $this->namespace['Graph'];
	}


	public function ilm(): Ilm
	{
		if (!isset($this->namespace['Ilm'])) {
			$this->namespace['Ilm'] = new Ilm($this);
		}
		return $this->namespace['Ilm'];
	}


	public function indices(): Indices
	{
		if (!isset($this->namespace['Indices'])) {
			$this->namespace['Indices'] = new Indices($this);
		}
		return $this->namespace['Indices'];
	}


	public function inference(): Inference
	{
		if (!isset($this->namespace['Inference'])) {
			$this->namespace['Inference'] = new Inference($this);
		}
		return $this->namespace['Inference'];
	}


	public function ingest(): Ingest
	{
		if (!isset($this->namespace['Ingest'])) {
			$this->namespace['Ingest'] = new Ingest($this);
		}
		return $this->namespace['Ingest'];
	}


	public function license(): License
	{
		if (!isset($this->namespace['License'])) {
			$this->namespace['License'] = new License($this);
		}
		return $this->namespace['License'];
	}


	public function logstash(): Logstash
	{
		if (!isset($this->namespace['Logstash'])) {
			$this->namespace['Logstash'] = new Logstash($this);
		}
		return $this->namespace['Logstash'];
	}


	public function migration(): Migration
	{
		if (!isset($this->namespace['Migration'])) {
			$this->namespace['Migration'] = new Migration($this);
		}
		return $this->namespace['Migration'];
	}


	public function ml(): Ml
	{
		if (!isset($this->namespace['Ml'])) {
			$this->namespace['Ml'] = new Ml($this);
		}
		return $this->namespace['Ml'];
	}


	public function monitoring(): Monitoring
	{
		if (!isset($this->namespace['Monitoring'])) {
			$this->namespace['Monitoring'] = new Monitoring($this);
		}
		return $this->namespace['Monitoring'];
	}


	public function nodes(): Nodes
	{
		if (!isset($this->namespace['Nodes'])) {
			$this->namespace['Nodes'] = new Nodes($this);
		}
		return $this->namespace['Nodes'];
	}


	public function profiling(): Profiling
	{
		if (!isset($this->namespace['Profiling'])) {
			$this->namespace['Profiling'] = new Profiling($this);
		}
		return $this->namespace['Profiling'];
	}


	public function queryRuleset(): QueryRuleset
	{
		if (!isset($this->namespace['QueryRuleset'])) {
			$this->namespace['QueryRuleset'] = new QueryRuleset($this);
		}
		return $this->namespace['QueryRuleset'];
	}


	public function rollup(): Rollup
	{
		if (!isset($this->namespace['Rollup'])) {
			$this->namespace['Rollup'] = new Rollup($this);
		}
		return $this->namespace['Rollup'];
	}


	public function searchApplication(): SearchApplication
	{
		if (!isset($this->namespace['SearchApplication'])) {
			$this->namespace['SearchApplication'] = new SearchApplication($this);
		}
		return $this->namespace['SearchApplication'];
	}


	public function searchableSnapshots(): SearchableSnapshots
	{
		if (!isset($this->namespace['SearchableSnapshots'])) {
			$this->namespace['SearchableSnapshots'] = new SearchableSnapshots($this);
		}
		return $this->namespace['SearchableSnapshots'];
	}


	public function security(): Security
	{
		if (!isset($this->namespace['Security'])) {
			$this->namespace['Security'] = new Security($this);
		}
		return $this->namespace['Security'];
	}


	public function shutdown(): Shutdown
	{
		if (!isset($this->namespace['Shutdown'])) {
			$this->namespace['Shutdown'] = new Shutdown($this);
		}
		return $this->namespace['Shutdown'];
	}


	public function simulate(): Simulate
	{
		if (!isset($this->namespace['Simulate'])) {
			$this->namespace['Simulate'] = new Simulate($this);
		}
		return $this->namespace['Simulate'];
	}


	public function slm(): Slm
	{
		if (!isset($this->namespace['Slm'])) {
			$this->namespace['Slm'] = new Slm($this);
		}
		return $this->namespace['Slm'];
	}


	public function snapshot(): Snapshot
	{
		if (!isset($this->namespace['Snapshot'])) {
			$this->namespace['Snapshot'] = new Snapshot($this);
		}
		return $this->namespace['Snapshot'];
	}


	public function sql(): Sql
	{
		if (!isset($this->namespace['Sql'])) {
			$this->namespace['Sql'] = new Sql($this);
		}
		return $this->namespace['Sql'];
	}


	public function ssl(): Ssl
	{
		if (!isset($this->namespace['Ssl'])) {
			$this->namespace['Ssl'] = new Ssl($this);
		}
		return $this->namespace['Ssl'];
	}


	public function synonyms(): Synonyms
	{
		if (!isset($this->namespace['Synonyms'])) {
			$this->namespace['Synonyms'] = new Synonyms($this);
		}
		return $this->namespace['Synonyms'];
	}


	public function tasks(): Tasks
	{
		if (!isset($this->namespace['Tasks'])) {
			$this->namespace['Tasks'] = new Tasks($this);
		}
		return $this->namespace['Tasks'];
	}


	public function textStructure(): TextStructure
	{
		if (!isset($this->namespace['TextStructure'])) {
			$this->namespace['TextStructure'] = new TextStructure($this);
		}
		return $this->namespace['TextStructure'];
	}


	public function transform(): Transform
	{
		if (!isset($this->namespace['Transform'])) {
			$this->namespace['Transform'] = new Transform($this);
		}
		return $this->namespace['Transform'];
	}


	public function watcher(): Watcher
	{
		if (!isset($this->namespace['Watcher'])) {
			$this->namespace['Watcher'] = new Watcher($this);
		}
		return $this->namespace['Watcher'];
	}


	public function xpack(): Xpack
	{
		if (!isset($this->namespace['Xpack'])) {
			$this->namespace['Xpack'] = new Xpack($this);
		}
		return $this->namespace['Xpack'];
	}
}
