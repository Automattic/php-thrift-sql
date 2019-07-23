default: thrift

thrift: submodules

submodules:
	git submodule update

install: default
	rm -rf src/Thrift
	cp -a src-thrift/thrift/lib/php/lib src/Thrift

.PHONY: default thrift submodules install
