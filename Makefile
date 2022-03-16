GROUP = cms
DEV_IMAGE = $(GROUP)/logger
DOCKER_NETWORK = host
PHP_PATH = /usr/bin/php

ifeq ($(WORKSPACE),)
    MOUNTS:=-w /apps -v `pwd`/:/apps
else
    MOUNTS:=-w $(WORKSPACE)/ --volumes-from=$(HOSTNAME)
endif

define docker_run
	docker run --rm \
	    $(MOUNTS) \
	    ${1} \
	    ${2} \
	    ${3} \
	    ${4} \
	    ${5}
endef

dev-build:
	docker build -t $(DEV_IMAGE) .

composer-require-package:
	@read -p "Enter package name:" package; \
	$(call docker_run, $(DEV_IMAGE), composer require $$package -vvvv --ignore-platform-reqs)

composer-install:
	$(call docker_run, $(DEV_IMAGE), composer, install --ignore-platform-reqs)

composer-update:
	$(call docker_run, $(DEV_IMAGE), composer, update --ignore-platform-reqs)

# Run PHPUnit Tests
phpunit-tests:
	$(call docker_run, --network=$(DOCKER_NETWORK), --env-file .env.test, $(DEV_IMAGE), $(PHP_PATH) bin/phpunit -d memory_limit=-1)