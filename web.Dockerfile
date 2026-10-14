FROM php:8.2-fpm

# Default the runtime to prod. Can be overridden by passing in env vars
ARG RUNTIME_ENV=production
ARG CONFIG_LIB_DIR=/srv/lib
ARG CONFIG_WEB_DIR=/var/www/html
ARG CONFIG_ANALYTICS_DIR=/var/log/analytics

# Need zip and client for migrations and start script
RUN apt-get update -y && apt-get install -y libmariadb-dev libpq-dev zip mariadb-client postgresql-client vim

RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY docker/90-xdebug.ini "${PHP_INI_DIR}/config.d"

# Using the default php ini file for now
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy the php files into the container - for prod
# these will be overlayed with the dev env via compose
ADD ./site/php-migrations /var/php-migrations
ADD ./site/lib /srv/lib
ADD ./site/web /var/www/html
ADD ./scripts/start.sh /var/scripts/start.sh
ADD ./scripts/db-smoke.php /var/scripts/db-smoke.php

# Make analytics dir writable by the web server
RUN mkdir ${CONFIG_ANALYTICS_DIR} && chown www-data:www-data ${CONFIG_ANALYTICS_DIR}

# Install composer in the docker image, 
# and put in a place that composer will be in the path
RUN curl -sS https://getcomposer.org/installer | php -d allow_url_fopen=On -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
