FROM php:8.2-apache

# Default the runtime to prod. Can e over-ridden by passing in a env vars
ARG RUNTIME_ENV=production
ARG CONFIG_LIB_DIR=/srv/lib
ARG CONFIG_WEB_DIR=/var/www/html

# Need zip and client for migrations and start script
RUN apt-get update -y && apt-get install -y libmariadb-dev zip mariadb-client
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

# Using the default php ini file for now
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy the php files into the container - for prod
# these will be overlayed with the dev env via compose
ADD ./site/php-migrations /var/php-migrations
ADD ./site/lib /srv/lib
ADD ./site/web /var/www/html
ADD ./scripts/start.sh /var/scripts/start.sh

# Install composer in the docker image, 
# and put in a place that composer will be in the path
RUN curl -sS https://getcomposer.org/installer | php -d allow_url_fopen=On -- --install-dir=/usr/local/bin --filename=composer

#
WORKDIR /var/www/html
