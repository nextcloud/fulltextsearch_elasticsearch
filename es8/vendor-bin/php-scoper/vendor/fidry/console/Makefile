# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules


TYPED_INPUT = src/Input/TypedInput.php

COVERAGE_DIR = dist/coverage
COVERAGE_XML_DIR = $(COVERAGE_DIR)/coverage-xml
COVERAGE_JUNIT = $(COVERAGE_DIR)/phpunit.junit.xml
COVERAGE_HTML_DIR = $(COVERAGE_DIR)/html

INFECTION_BIN = vendor/bin/infection
INFECTION = SYMFONY_DEPRECATIONS_HELPER="disabled=1" php -d zend.enable_gc=0 $(INFECTION_BIN) --skip-initial-tests --coverage=$(COVERAGE_DIR) --only-covered --show-mutations --min-msi=100 --min-covered-msi=100 --ansi --threads=max
INFECTION_WITH_INITIAL_TESTS = SYMFONY_DEPRECATIONS_HELPER="disabled=1" php -d zend.enable_gc=0 $(INFECTION_BIN) --only-covered --show-mutations --min-msi=100 --min-covered-msi=100 --ansi --threads=max

PHPUNIT_BIN = vendor/bin/phpunit
PHPUNIT = php -d zend.enable_gc=0 $(PHPUNIT_BIN)
PHPUNIT_COVERAGE_INFECTION = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-xml=$(COVERAGE_XML_DIR) --log-junit=$(COVERAGE_JUNIT)
PHPUNIT_COVERAGE_HTML = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html=$(COVERAGE_HTML_DIR)

PSALM_BIN = vendor-bin/psalm/vendor/vimeo/psalm/psalm
PSALM = $(PSALM_BIN) --no-cache

PHP_CS_FIXER_BIN = vendor-bin/php-cs-fixer/vendor/friendsofphp/php-cs-fixer/php-cs-fixer
PHP_CS_FIXER = PHP_CS_FIXER_IGNORE_ENV=1 $(PHP_CS_FIXER_BIN) fix --ansi --verbose --config=.php-cs-fixer.php


.DEFAULT_GOAL := check


#
# Command
#---------------------------------------------------------------------------

.PHONY: help
help: 	## Shows the help
help:
	@printf "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'


.PHONY: dump
dump:	## Dumps the getter
dump:
	$(MAKE) $(TYPED_INPUT)


.PHONY: check
check:  ## Runs all the checks
check: cs autoreview infection


.PHONY: autoreview
autoreview: ## Runs the AutoReview checks
autoreview: cs_lint psalm phpunit_autoreview


.PHONY: test
test: 	    ## Runs the tests
test: composer_validate_package infection


.PHONY: cs
cs: 	    ## Runs the CS fixers
cs: gitignore_sort composer_normalize php_cs_fixer


.PHONY: cs_lint
cs_lint:    ## Runs the CS linters
cs_lint: composer_normalize_lint php_cs_fixer_lint


.PHONY: composer_normalize
composer_normalize: vendor
	composer normalize

.PHONY: composer_normalize_lint
composer_normalize_lint: vendor
	composer normalize --dry-run

.PHONY: gitignore_sort
gitignore_sort:
	LC_ALL=C sort -u .gitignore -o .gitignore
	LC_ALL=C sort -u .gitattributes -o .gitattributes

.PHONY: php_cs_fixer
php_cs_fixer: $(PHP_CS_FIXER_BIN)
	$(PHP_CS_FIXER)

.PHONY: php_cs_fixer_lint
php_cs_fixer_lint: $(PHP_CS_FIXER_BIN)
	$(PHP_CS_FIXER) --dry-run --verbose

.PHONY: psalm
psalm: $(PSALM_BIN) vendor
	$(PSALM)

.PHONY: infection
infection: $(INFECTION_BIN) vendor
	$(INFECTION_WITH_INITIAL_TESTS)

.PHONY: _infection
_infection: $(INFECTION_BIN) $(COVERAGE_XML_DIR) $(COVERAGE_JUNIT) vendor
	$(INFECTION)

.PHONY: composer_validate_package
composer_validate_package: vendor
	composer validate --strict

.PHONY: phpunit
phpunit: $(PHPUNIT_BIN) vendor
	$(PHPUNIT) --testsuite=Tests

.PHONY: phpunit_autoreview
phpunit_autoreview: $(PHPUNIT_BIN) vendor
	$(PHPUNIT) --testsuite=AutoReview

.PHONY: phpunit_coverage_infection
phpunit_coverage_infection: ## Runs PHPUnit tests with test coverage
phpunit_coverage_infection: $(PHPUNIT_BIN) vendor
	$(PHPUNIT_COVERAGE_INFECTION) --testsuite=Tests

.PHONY: phpunit_coverage_html
phpunit_coverage_html:	    ## Runs PHPUnit with code coverage with HTML report
phpunit_coverage_html: $(PHPUNIT_BIN) vendor
	$(PHPUNIT_COVERAGE_HTML) --testsuite=Tests
	@echo "You can check the report by opening the file \"$(COVERAGE_HTML_DIR)/index.html\"."

.PHONY: clean
clean:  ## Cleans up all artefacts
clean:
	@# Legacy entries.
	@rm -f \
		.php-cs-fixer.cache \
		.phpunit.result.cache \
		infection.log \
		|| true

	rm -rf \
		tests/Integration/**/cache \
		dist \
		|| true

	$(MAKE) dist


.PHONY: install_symfony5
install_symfony5: ## Installs latest dependencies with Symfony5
install_symfony5: vendor
	SYMFONY_REQUIRE="5.4.*" composer update --no-scripts
	touch -c vendor $(PHPUNIT_BIN) $(INFECTION_BIN)


.PHONY: install_symfony6
install_symfony6: ## Installs latest dependencies with Symfony6
install_symfony6: vendor
	SYMFONY_REQUIRE="6.*.*" composer update --no-scripts
	touch -c vendor $(PHPUNIT_BIN) $(INFECTION_BIN)


#
# Rules
#---------------------------------------------------------------------------

vendor_install: composer.json $(wildcard composer.lock)
	composer update --no-scripts
	touch -c vendor
	touch -c $(PHPUNIT_BIN)
	touch -c $(INFECTION_BIN)

vendor:
	$(MAKE) vendor_install

$(PHPUNIT_BIN): vendor
	touch -c $@

$(INFECTION_BIN): vendor
	touch -c $@

$(COVERAGE_XML_DIR): $(PHPUNIT_BIN) src tests phpunit.xml.dist
	$(PHPUNIT_COVERAGE_INFECTION)
	touch -c $@
	touch -c $(COVERAGE_JUNIT)

$(COVERAGE_JUNIT): $(PHPUNIT_BIN) src tests phpunit.xml.dist
	$(PHPUNIT_COVERAGE_INFECTION)
	touch -c $@
	touch -c $(COVERAGE_XML_DIR)

php_cs_fixer_install: $(PHP_CS_FIXER_BIN)
	# Nothing to do

$(PHP_CS_FIXER_BIN): vendor
	composer bin php-cs-fixer install
	touch -c $@

psalm_install: $(PSALM_BIN)
	# Nothing to do

$(PSALM_BIN): vendor
	composer bin psalm install
	touch -c $@

$(TYPED_INPUT): src vendor
	./bin/dump-getters
	touch -c $@

dist:
	mkdir -p dist
	touch dist/.gitkeep
