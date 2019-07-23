default: impala hive thrift
	# Lint generated files
	find build/gen-php -type f -name "*.php" -print0 | xargs -0L1 \
		php -l

impala: submodules
	# Build error codes thrift file
	src-thrift/impala/common/thrift/generate_error_codes.py
	mv ErrorCodes.thrift build/
	# Remap some dirs to fix file inclusion
	@rm -f build/share
	ln -s ../src-thrift/thrift/contrib build/share
	# Build Impala
	thrift --gen php \
		-o build \
		-I build \
		-I src-thrift/hive/metastore/if \
		-I src-thrift/hive/service/if \
		src-thrift/impala/common/thrift/ImpalaService.thrift
	thrift --gen php \
		-o build \
		-I build \
		-I src-thrift/hive/metastore/if \
		src-thrift/impala/common/thrift/beeswax.thrift

hive: submodules
	thrift --gen php \
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
	mv build/gen-php src/ThriftGenerated

clean:
	rm -rf build
	rm -rf src/Thrift
	rm -rf src/ThriftGenerated
	rm -f src/.php-autoload-generator-cache.json

.PHONY: default impala hive thrift submodules install clean
