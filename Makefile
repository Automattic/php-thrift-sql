default: hive thrift
	# Lint generated files
	find gen-php -type f -name "*.php" -print0 | xargs -0L1 \
		php -l

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
	rm -rf src/Thrift
	cp -a src-thrift/thrift/lib/php/lib src/Thrift
	rm -rf src/ThriftGenerated
	mv build/gen-php src/ThriftGenerated

clean:
	@mkdir -p build
	rm -rf build

.PHONY: default hive thrift submodules install clean
