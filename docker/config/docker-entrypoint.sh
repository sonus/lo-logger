#!/bin/sh
set -e

if [ "$1" = '/usr/bin/supervisord' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	if [ "$APP_ENV" = 'dev' ]; then
		composer install --prefer-dist --no-progress --no-suggest --no-interaction
		composer dump-env dev
		bin/console cache:clear
	else
		composer dump-env prod --empty
		APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
		rm -f .env
	fi

	echo "Waiting for db to be ready..."
	until bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
		sleep 1
	done

	if [ "$APP_ENV" != 'prod' ]; then
		echo "Creating database using doctrine..."
		bin/console doctrine:database:create -q || true
	fi

	if [ "$APP_ENV" = "dev" ]; then
		echo "Run database migrations"
		# bin/console doctrine:migrations:migrate --no-debug --no-interaction
	fi
fi

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf