# Makefile for building the project

all: install

clean:
	rm -rf vendor
	rm -rf node_modules
	rm -rf js/build

install:
	composer install --dev
	npm install
	npm run build
