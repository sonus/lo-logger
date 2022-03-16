#!/bin/sh
set -e

if [ "$1" = '/usr/bin/supervisord' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then

		echo "Install crontabs"
		crontab /var/www/html/config/crontabs

		echo "Run cron"
		crond -f &

fi

exec docker-php-entrypoint "$@"