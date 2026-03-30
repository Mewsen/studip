RESOURCES = $(shell find src -type f)

# build all needed files
build: dist

npm: node_modules/.package-lock.json

node_modules/.package-lock.json: package.json package-lock.json
	npm install --no-save --no-audit --no-fund

dist: node_modules/.package-lock.json $(RESOURCES)
	npm run build

storybook: dist
	npm run storybook

# remove all generated files
clean: clean-npm clean-dist

clean-npm:
	rm -rf node_modules

clean-dist:
	rm -rf dist
