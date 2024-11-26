# Full text search - OpenSearch

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nextcloud/FullTextSearch_OpenSearch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nextcloud/FullTextSearch_OpenSearch/?branch=master)

_nextcloud - OpenSearch_ is an extension to the _Full text search_ framework. It allows you to index your content with an OpenSearch platform. It is a fork of the https://github.com/nextcloud/fulltextsearch_elasticsearch app to allow connecting to an OpenSearch engine. 

## Compatibility

OpenSearch: 1.0.0-2.x.x

OpenSearch has been forked 2021 due to a change of the ElasticSearch license, whereas OpenSearch is published under the OSI certified Apache license. OpenSearch includes for free cross-cluster replication, LDAP/OpenID/SAML authentication, IP filtering, configurable retention period, anomaly detection, tableau connector, or machine learning features whereas those features are non-free with ElasticSearch and would require a subscription.

### Documentation

You have to run an OpenSearch service on an extra VM / Docker and install three Nextcloud apps.

#### OpenSearch
- install ingest-attachment e.g. `./bin/opensearch-plugin install ingest-attachment`
- create a role with permissions
    - cluster:
      - cluster:monitor/main
      - cluster:monitor/main
    - index:
      - index: NEXTCLOUD_INDEX*
      
        permissions: indices_all
- create a user and assign the role

#### Nextcloud
- install `fulltextsearch` app
- install `files_fulltextsearch` app
- extract nextcloud_opensearch.tar.gz to your apps folder e.g. /var/www/apps
- set file ownership e.g. `chown www-data:www-data /var/www/apps/nextcloud_opensearch -R`
- enable the app in nextcloud
- configure the plugin, by selecting "OpenSearch" in "Full Text Search" on the Nextcloud "Administrative Settings" page
- run `occ fulltextsearch:index`, to create an initial index 

#### build

- make sure you have PHP 8 and composer installed
- run `./make`
