# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

# General variables
TOUCH = bash .makefile/touch.sh

# PHP variables
COMPOSER = $(shell which composer)
COVERAGE_DIR = dist/coverage
COVERS_VALIDATOR_BIN = vendor-bin/covers-validator/vendor/ockcyp/covers-validator/covers-validator
COVERS_VALIDATOR = $(COVERS_VALIDATOR_BIN)
INFECTION_BIN = vendor/bin/infection
INFECTION = php -d zend.enable_gc=0 $(INFECTION_BIN) --skip-initial-tests --coverage=$(COVERAGE_DIR) --only-covered --threads=4 --min-msi=100 --min-covered-msi=100 --ansi
PHPUNIT_BIN = vendor/bin/phpunit
PHPUNIT = php -d zend.enable_gc=0 $(PHPUNIT_BIN)
PHPUNIT_COVERAGE = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-xml=$(COVERAGE_DIR)/coverage-xml --log-junit=$(COVERAGE_DIR)/phpunit.junit.xml
PSALM_BIN = vendor-bin/psalm/vendor/vimeo/psalm/psalm
PSALM = $(PSALM_BIN) --no-cache
PHP_CS_FIXER_BIN = vendor-bin/php-cs-fixer/vendor/friendsofphp/php-cs-fixer/php-cs-fixer
# To keep in sync with the command defined in the parent Makefile
PHP_CS_FIXER = $(PHP_CS_FIXER_BIN) fix --ansi --verbose --config=.php-cs-fixer.php


.DEFAULT_GOAL := default


#
# Command
#---------------------------------------------------------------------------

.PHONY: help
help: ## Shows the help
help:
	@printf "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'


.PHONY: default
default: ## Runs the default task: CS fix and all the tests
default: cs test


.PHONY: cs
cs: ## Runs PHP-CS-Fixer
cs: $(PHP_CS_FIXER_BIN)
	$(PHP_CS_FIXER)


.PHONY: psalm
psalm: ## Runs Psalm
psalm: $(PSALM_BIN) vendor
	$(PSALM)


.PHONY: infection
infection: ## Runs infection
infection: $(INFECTION_BIN) $(COVERAGE_DIR) vendor
	if [ -d $(COVERAGE_DIR)/coverage-xml ]; then $(INFECTION); fi


.PHONY: test
test: ## Runs all the tests
test: clear-cache validate-package covers-validator psalm coverage infection


.PHONY: validate-package
validate-package: ## Validates the Composer package
validate-package: vendor
	composer validate --strict


.PHONY: covers-validator
covers-validator: ## Validates the PHPUnit @covers annotations
covers-validator: $(COVERS_VALIDATOR_BIN) vendor
	$(COVERS_VALIDATOR)


.PHONY: phpunit
phpunit: ## Runs PHPUnit
phpunit: $(PHPUNIT_BIN) vendor
	$(PHPUNIT)


.PHONY: coverage
coverage: ## Runs PHPUnit with code coverage
coverage: $(PHPUNIT_BIN) vendor
	$(PHPUNIT_COVERAGE)


.PHONY: clear-cache
clear-cache: ## Clears the integration test app cache
clear-cache:
	rm -rf tests/Integration/**/cache || true


#
# Rules
#---------------------------------------------------------------------------

# Vendor does not depend on the composer.lock since the later is not tracked
# or committed.
vendor: composer.json
	$(COMPOSER) update --no-scripts
	$(TOUCH) "$@"

$(PHPUNIT_BIN): vendor
	$(TOUCH) "$@"

$(INFECTION_BIN): vendor
	$(TOUCH) "$@"

$(COVERAGE_DIR): $(PHPUNIT_BIN) src tests phpunit.xml.dist
	$(PHPUNIT_COVERAGE)
	$(TOUCH) "$@"

$(PHP_CS_FIXER_BIN):
	$(MAKE) --directory ../.. vendor-bin/php-cs-fixer/vendor

$(PSALM_BIN):
	$(MAKE) --directory ../.. vendor-bin/psalm/vendor/bin/psalm

$(COVERS_VALIDATOR_BIN):
	$(MAKE) --directory ../.. vendor-bin/covers-validator/vendor/bin/covers-validator
