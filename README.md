# fullnextsearch_elasticsearch

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/daita/fullnextsearch_elasticsearch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/daita/fullnextsearch_elasticsearch/?branch=master)

**ALPHA - DO NOT INSTALL ON PROD ENVIRONMENT.**  

[**FullNextSearch**](https://github.com/nextcloud/nextant/tree/fullnextsearch) allows you to index and search the content of your cloud.  

This app is a module that will add a _Search Platform Gateway_ to **FullNextSearch**, please have a look to the [README of the core app](https://github.com/nextcloud/nextant/blob/fullnextsearch/README.md) first



### Installation

You can download the app from the store, or download the source from the git repository and copy it in **apps/**.  
If you choose to install from the source, you will need _composer_ to download the 3rd party dependencies

>      make composer



### Configuration

Set ElasticSearch as the platform
>     ./occ config:app:set --value 'OCA\FullNextSearch_ElasticSearch\Platform\ElasticSearchPlatform' fullnextsearch search_platform

Set the address to reach elastic search 
>     ./occ config:app:set --value 'http://username:password@localhost:9200' fullnextsearch_elasticsearch elastic_host

Set the index for this nextcloud 
>     ./occ config:app:set --value 'my_index' fullnextsearch_elasticsearch elastic_index


