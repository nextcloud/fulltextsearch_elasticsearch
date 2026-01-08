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
INFECTION = php -d zend.enable_gc=0 $(INFECTION_BIN) --skip-initial-tests --coverage=$(COVERAGE_DIR) --only-covered --show-mutations --min-msi=100 --min-covered-msi=100 --ansi --threads=$(shell nproc || sysctl -n hw.ncpu || 1)
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
default: src/Input/TypedInput.php cs test


.PHONY: dump
dump:	## Dumps the getter
dump:
	$(MAKE) --always-make src/Input/TypedInput.php


.PHONY: cs
cs: ## Runs PHP-CS-Fixer
cs: $(PHP_CS_FIXER_BIN)
ifndef SKIP_CS
	$(PHP_CS_FIXER)
endif


.PHONY: psalm
psalm: ## Runs Psalm
psalm: $(PSALM_BIN) vendor
ifndef SKIP_PSALM
	$(PSALM)
endif


.PHONY: infection
infection: ## Runs infection
infection: $(INFECTION_BIN) $(COVERAGE_DIR) vendor
ifndef SKIP_INFECTION
	if [ -d $(COVERAGE_DIR)/coverage-xml ]; then $(INFECTION); fi
endif

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
ifndef SKIP_COVERS_VALIDATOR
	$(COVERS_VALIDATOR)
endif


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
	touch -c $@
	touch -c $(PHPUNIT_BIN)
	touch -c $(INFECTION_BIN)

$(PHPUNIT_BIN): vendor
	touch -c $@

$(INFECTION_BIN): vendor
	touch -c $@

$(COVERAGE_DIR): $(PHPUNIT_BIN) src tests phpunit.xml.dist
	$(PHPUNIT_COVERAGE)
	touch -c $@

$(PHP_CS_FIXER_BIN): vendor
ifndef SKIP_CS
	composer bin php-cs-fixer install
	touch -c $@
endif

$(PSALM_BIN): vendor
ifndef SKIP_PSALM
	composer bin psalm install
	touch -c $@
endif

$(COVERS_VALIDATOR_BIN): vendor
ifndef SKIP_COVERS_VALIDATOR
	composer bin covers-validator install
	touch -c $@
endif

src/Input/TypedInput.php: src vendor
	./bin/dump-getters
	touch -c $@
