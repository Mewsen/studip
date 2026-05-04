CODECEPT  = composer/bin/codecept
CATALOGS  = locale/en/LC_MESSAGES/studip.mo locale/en/LC_MESSAGES/js-resources.json
RESOURCES = $(shell find resources packages/studip-ui/src -type f)

PHP_SOURCES = $(shell find app config lib public templates -name '*.php' \( ! -path 'public/plugins_packages/*' -o -path 'public/plugins_packages/core/*' \))
VUE_SOURCES = $(shell find resources -name '*.js' -o -name '*.vue')

# build all needed files
build: composer assets-prod

# remove all generated files
clean: clean-composer clean-npm clean-assets clean-doc

composer: composer/composer/installed.json

composer-dev: $(CODECEPT)

composer/composer/installed.json: composer.json composer.lock
	composer install --no-dev
	@touch $@

$(CODECEPT): composer.json composer.lock
	composer install
	@touch $@

clean-composer:
	rm -rf composer

npm: node_modules/.package-lock.json

node_modules/.package-lock.json: package.json package-lock.json
	npm install --no-save --no-audit --no-fund

clean-npm:
	rm -rf node_modules

assets-dev: .assets.dev

assets-prod: .assets.prod

assets-watch: npm
	npm run watch

.assets.dev: node_modules/.package-lock.json $(RESOURCES)
	@rm -f .assets.prod
	npm run build -- --mode=development
	@touch $@

.assets.prod: node_modules/.package-lock.json $(RESOURCES)
	@rm -f .assets.dev
	npm run build -- --mode=production
	@touch $@

clean-assets:
	@rm -f .assets.dev .assets.prod
	rm -rf public/assets/javascripts/*.js
	rm -rf public/assets/javascripts/*.js.map
	rm -rf public/assets/stylesheets/*.css
	rm -rf public/assets/stylesheets/*.css.map

doc: force_update
	doxygen Doxyfile

clean-doc:
	rm -rf doc/html

test: test-unit

test-functional: $(CODECEPT)
	$(CODECEPT) run functional

test-jsonapi: $(CODECEPT)
	$(CODECEPT) run jsonapi

test-e2e: npm
	npx playwright test

test-unit: $(CODECEPT)
	$(CODECEPT) run unit

catalogs: npm $(CATALOGS)

clean-icons:
	find public/assets/images/icons -type f -not -path '*blue*' -delete

optimize-icons: npm
	find public/assets/images/icons/blue -type f | xargs -P0 npx svgo -q --config=config/svgo.config.js

icons: optimize-icons
	find public/assets/images/icons/blue -type f -print0 | xargs -0 -n1 -I{} echo 'sed "s/#28497c/#000000/g" {} > {}' | sed 's#icons/blue#icons/black#2' | sh
	find public/assets/images/icons/blue -type f -print0 | xargs -0 -n1 -I{} echo 'sed "s/#28497c/#00962d/g" {} > {}' | sed 's#icons/blue#icons/green#2' | sh
	find public/assets/images/icons/blue -type f -print0 | xargs -0 -n1 -I{} echo 'sed "s/#28497c/#6e6e6e/g" {} > {}' | sed 's#icons/blue#icons/grey#2' | sh
	find public/assets/images/icons/blue -type f -print0 | xargs -0 -n1 -I{} echo 'sed "s/#28497c/#cb1800/g" {} > {}' | sed 's#icons/blue#icons/red#2' | sh
	find public/assets/images/icons/blue -type f -print0 | xargs -0 -n1 -I{} echo 'sed "s/#28497c/#ffffff/g" {} > {}' | sed 's#icons/blue#icons/white#2' | sh
	find public/assets/images/icons/blue -type f -print0 | xargs -0 -n1 -I{} echo 'sed "s/#28497c/#ffad00/g" {} > {}' | sed 's#icons/blue#icons/yellow#2' | sh

# default rules for gettext handling
js-%.pot: $(VUE_SOURCES)
	npx gettext-extract --attribute v-translate --output $@ $(VUE_SOURCES)

js-%.po: js-%.pot
	msgmerge -qU -C $(dir $@)studip.po $@ $<

js-%.json: js-%.po
	npx gettext-compile --output $@ $<
	sed -i~ 's/^{[^{]*//;s/}$$//' $@

%.pot: $(PHP_SOURCES)
	xgettext -o $@ --from-code=UTF-8 $(PHP_SOURCES)

%.po: %.pot
	msgmerge -qU $@ $<

%.mo: %.po
	msgfmt -o $@ $<

# dummy target to force update of "doc" target
force_update:
