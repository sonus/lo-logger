#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	mkdir -p var/cache var/log
	setfacl -R -m u:"$(whoami)":rwX var
	setfacl -dR -m u:"$(whoami)":rwX var

    if [ "$APP_ENV" != 'prod' ]; then
        composer dump-env "$APP_ENV"
	    bin/console cache:clear
	else
	    composer dump-env prod --empty
	    APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
	fi

	if [ "$APP_ENV" != 'prod' ]; then
		composer install --prefer-dist --no-progress --no-suggest --no-interaction
	fi

    echo "Waiting for db to be ready..."
    until bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    	sleep 1
    done
fi


exec docker-php-entrypoint "$@"