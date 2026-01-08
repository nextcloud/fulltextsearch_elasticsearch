# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

# PHP variables
COMPOSER = $(shell which composer)

SRC_TESTS_FILES=$(shell find src/ tests/ -type f) phpunit.xml.dist
COVERAGE_DIR = dist/coverage
COVERAGE_XML = $(COVERAGE_DIR)/xml
COVERAGE_HTML = $(COVERAGE_DIR)/html
TARGET_MSI = 100

INFECTION_BIN = vendor/bin/infection
INFECTION = php -d zend.enable_gc=0 $(INFECTION_BIN) --skip-initial-tests --coverage=$(COVERAGE_DIR) --only-covered --show-mutations --min-msi=100 --min-covered-msi=100 --ansi

PHPUNIT_BIN = vendor/bin/phpunit
PHPUNIT = php -d zend.enable_gc=0 $(PHPUNIT_BIN)
PHPUNIT_COVERAGE_INFECTION = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-xml=$(COVERAGE_DIR)/coverage-xml --log-junit=$(COVERAGE_DIR)/phpunit.junit.xml
PHPUNIT_COVERAGE_HTML = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html=$(COVERAGE_HTML)
PHPUNIT_COVERAGE = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-xml=$(COVERAGE_DIR)/coverage-xml --log-junit=$(COVERAGE_DIR)/phpunit.junit.xml

PHP_CS_FIXER_BIN = vendor-bin/php-cs-fixer/vendor/friendsofphp/php-cs-fixer/php-cs-fixer
PHP_CS_FIXER = $(PHP_CS_FIXER_BIN) fix --ansi --verbose --config=.php-cs-fixer.php

RECTOR_BIN = vendor-bin/rector/vendor/bin/rector
RECTOR = $(RECTOR_BIN)


.DEFAULT_GOAL := default


#
# Commands
#---------------------------------------------------------------------------

.PHONY: help
help:
	@printf "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'


.PHONY: default
default:   ## Runs the default task: CS fix and all the tests
default: cs test

.PHONY: cs
cs: 	   ## Fixes CS
cs: gitignore_sort composer_normalize rector php_cs_fixer

.PHONY: cs_lint
cs_lint:   ## Lints CS
cs_lint: composer_normalize_lint rector_lint php_cs_fixer_lint

.PHONY: gitignore_sort
gitignore_sort:
	LC_ALL=C sort -u .gitignore -o .gitignore

.PHONY: composer_normalize
composer_normalize: vendor
	$(COMPOSER) normalize

.PHONY: composer_normalize_lint
composer_normalize_lint: vendor
	$(COMPOSER) normalize --dry-run

.PHONY: php_cs_fixer
php_cs_fixer: $(PHP_CS_FIXER_BIN)
	$(PHP_CS_FIXER)

.PHONY: php_cs_fixer_lint
php_cs_fixer_lint: $(PHP_CS_FIXER_BIN) dist
	$(PHP_CS_FIXER)

.PHONY: rector
rector: $(RECTOR_BIN)
ifndef SKIP_RECTOR
	$(RECTOR)
endif

.PHONY: rector_lint
rector_lint: $(RECTOR_BIN) dist
ifndef SKIP_RECTOR
	$(RECTOR) --dry-run
endif

.PHONY: test
test:	   ## Runs all the tests
test: composer_validate infection

.PHONY: composer_validate
composer_validate: ## Validates the Composer package
composer_validate: vendor
	composer validate --strict

.PHONY: phpunit
phpunit:   ## Runs PHPUnit
phpunit: $(PHPUNIT_BIN) dist vendor
	$(PHPUNIT)

.PHONY: phpunit_coverage_infection
phpunit_coverage_infection: ## Runs PHPUnit with code coverage for Infection
phpunit_coverage_infection: $(PHPUNIT_BIN) dist vendor
	$(PHPUNIT_COVERAGE_INFECTION)

.PHONY: phpunit_coverage_html
phpunit_coverage_html: ## Runs PHPUnit with code coverage with HTML report
phpunit_coverage_html: $(PHPUNIT_BIN) dist vendor
	$(PHPUNIT_COVERAGE_HTML)
	@echo "You can check the report by opening the file \"$(COVERAGE_HTML)/index.html\"."

.PHONY: infection
infection: ## Runs infection
infection: $(INFECTION_BIN) $(COVERAGE_DIR) dist vendor
	if [ -d $(COVERAGE_DIR)/coverage-xml ]; then $(INFECTION); fi


#
# Rules
#---------------------------------------------------------------------------

# Vendor does not depend on the composer.lock since the later is not tracked
# or committed.
vendor: composer.json
	$(COMPOSER) update --no-scripts
	touch -c $@
	touch -c $(PHPUNIT_BIN)
	touch -c $(INFECTION_BIN)

$(PHPUNIT_BIN): vendor
	touch -c $@

$(INFECTION_BIN): vendor
	touch -c $@

$(COVERAGE_DIR): $(PHPUNIT_BIN) $(SRC_TESTS_FILES) phpunit.xml.dist
	$(PHPUNIT_COVERAGE)
	touch -c $@

$(PHP_CS_FIXER_BIN): vendor
ifndef SKIP_CS
	composer bin php-cs-fixer install
	touch -c $@
endif

$(RECTOR_BIN): vendor
ifndef SKIP_RECTOR
	composer bin rector install
	touch -c $@
endif

dist:
	mkdir -p dist
	touch $@
