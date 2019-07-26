
#
# Startup Checks / Utils
#

# Check for prerequisites exist in our PATH
EXECUTABLES = php thrift composer
EXECUTABLES_OK := $(foreach EXEC, $(EXECUTABLES), \
	$(if $(shell which $(EXEC)),some string, \
		$(error Could not find '$(EXEC)') \
	) \
)

# Detect multithread limit
PLATFORM=$(shell uname -s)
ifeq ($(PLATFORM),Darwin)
THREADS := $(shell sysctl -n hw.ncpu)
else ifeq ($(PLATFORM),Linux)
THREADS := $(shell nproc)
else
THREADS := 2
endif

#
# Make Targets
#

default: impala hive thrift
	# Lint generated files
	find build/gen-php/ThriftGenerated -type f -name "*.php" -print0 | \
		xargs -0L1 -P ${THREADS} \
		php -l

impala: submodules
	# Build error codes thrift file
	src-thrift/impala/common/thrift/generate_error_codes.py
	mv ErrorCodes.thrift build/
	# Remap some dirs to fix file inclusion
	@rm -f build/share
	ln -s ../src-thrift/thrift/contrib build/share
	# Build Impala
	thrift --gen php:nsglobal=ThriftGenerated \
		-o build \
		-I build \
		-I src-thrift/hive/metastore/if \
		-I src-thrift/hive/service/if \
		src-thrift/impala/common/thrift/ImpalaService.thrift
	thrift --gen php:nsglobal=ThriftGenerated \
		-o build \
		-I build \
		-I src-thrift/hive/metastore/if \
		src-thrift/impala/common/thrift/beeswax.thrift

hive: submodules
	thrift --gen php:nsglobal=ThriftGenerated \
		-o build \
		-I src-thrift/hive \
		src-thrift/hive/service/if/TCLIService.thrift

thrift: submodules

submodules:
	git submodule update
	@mkdir -p build

install: default
	@rm -rf src/Thrift
	cp -a src-thrift/thrift/lib/php/lib src/Thrift
	@rm -rf src/ThriftGenerated
	mv build/gen-php/ThriftGenerated src/ThriftGenerated

phar: install composer.lock
	vendor/bin/phpab --nolower --output src/autoload.php --basedir src src
	php -d phar.readonly=0 build.php

clean-dev: clean
	rm -rf vendor
	rm -f composer.lock

clean:
	rm -rf build
	rm -rf src/Thrift
	rm -rf src/ThriftGenerated
	rm -f src/autoload.php

composer.lock: composer.json
	composer install

.PHONY: default impala hive thrift submodules install phar clean-dev clean
