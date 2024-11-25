# Full text search - OpenSearch

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nextcloud/FullTextSearch_OpenSearch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nextcloud/FullTextSearch_OpenSearch/?branch=master)

_Full text search - OpenSearch_ is an extension to the _Full text search_ framework

It allows you to index your content into an OpenSearch platform.

## Compatibility

OpenSearch: 1.0.0-2.x.x

### Documentation

- install `fulltextsearch` app
- install `files_fulltestsearch` app
- extract fulltextsearch_opensearch.tar.gz to your apps folder e.g. /var/www/apps
- set file ownership e.g. `chown www-data:www-data /var/www/apps/fulltextsearch_opensearch -R`
- enable the app in nextcloud
- configure the plugin, by selecting "OpenSearch" in "Full Text Search" on the Nextcloud "Administrative Settings" page
- run `occ fulltextsearch:index`, to create an initial index 

#### build

- make sure you have PHP 8 and composer installed
- run `./make`
