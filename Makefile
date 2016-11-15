# Makefile for building the project

all: install-deps

clean:
	rm -rf vendor

composer.phar:
	curl -sS https://getcomposer.org/installer | php

install-deps: install-composer-deps

install-composer-deps: composer.phar
	php composer.phar install

update-composer: composer.phar
	rm -f composer.lock
	php composer.phar install --prefer-dist

