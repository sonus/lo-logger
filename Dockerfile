ARG PHP_VERSION=8.1

FROM php:${PHP_VERSION}-fpm-alpine AS base_api_platform

RUN apk add --no-cache \
		acl \
		file \
		gettext \
		mysql \
		git \
		shadow \
		nginx   \
		supervisor \
	;

RUN set -ex \
  	&& apk update \
    && apk add --no-cache libsodium \
    && apk add --no-cache --virtual build-dependencies g++ make autoconf libsodium-dev\
    && docker-php-source extract \
    && pecl install libsodium \
    && pecl install redis \
    && docker-php-ext-enable sodium \
    && docker-php-ext-enable redis \
    && docker-php-source delete \
    && cd  / && rm -fr /src \
    && apk del build-dependencies \
    && rm -rf /tmp/*

ARG APCU_VERSION=5.1.18
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-install -j "$(getconf _NPROCESSORS_ONLN)" \
		intl \
		pdo \
		pdo_mysql \
		zip \
		sockets \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/php.ini

RUN apk update
RUN apk add curl bash gzip
RUN curl -sS https://get.symfony.com/cli/installer | bash

RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisor.d/supervisor-fpm.ini /etc/supervisor.d/supervisor.ini

RUN chmod 644 /etc/supervisord.conf && touch /var/log/supervisord.log && chmod 777 /var/log/supervisord.log


ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

RUN mkdir -p /var/log/newrelic /run/nginx/

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["/usr/bin/supervisord", "--nodaemon", "--configuration", "/etc/supervisord.conf"]