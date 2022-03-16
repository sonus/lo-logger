FROM alpine:3.15

ARG PHP_VERSION="8.0.16-r0"

# DevNote: Add go to the apk add for ngnix nginx-log-generator
# Install packages and remove default server definition
RUN apk --no-cache add go php8=${PHP_VERSION} \
    php8-ctype \
    php8-curl \
    php8-dom \
    php8-exif \
    php8-fileinfo \
    php8-fpm \
    php8-gd \
    php8-iconv \
    php8-intl \
    php8-mbstring \
    php8-mysqli \
    php8-opcache \
    php8-openssl \
    php8-pecl-imagick \
    php8-pecl-redis \
    php8-phar \
    php8-session \
    php8-simplexml \
    php8-soap \
    php8-xml \
    php8-xmlreader \
    php8-zip \
    php8-zlib \
    php8-pdo \
    php8-xmlwriter \
    php8-tokenizer \
    php8-pdo_mysql \
    nginx supervisor curl tzdata htop mysql-client dcron

# Symlink php8 => php
RUN ln -s /usr/bin/php8 /usr/bin/php

# Install PHP tools
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Configure nginx
COPY docker/config/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY docker/config/fpm-pool.conf /etc/php8/php-fpm.d/www.conf
COPY docker/config/php.ini /etc/php8/conf.d/custom.ini

# Configure supervisord
COPY docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

#Adding Entrypoint
COPY docker/config/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

# Dev Setup only for generater
COPY development/main.go ./
COPY development/go.mod ./
COPY development/go.sum ./
RUN go build

# Setup document root
RUN mkdir -p /var/www/html

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody /var/www/html && \
  chown -R nobody.nobody /run && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/log/nginx

RUN chmod +x /usr/local/bin/docker-entrypoint

# Switch to use a non-root user from here on
USER nobody

# Add application
WORKDIR /var/www/html
COPY --chown=nobody src/ /var/www/html/

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
ENTRYPOINT ["docker-entrypoint"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping
