FROM php:8.2-apache
# Need zip and client for migrations and start script
RUN apt-get update -y && apt-get install -y libmariadb-dev zip mariadb-client
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

# Install composer in the docker image, 
# and put in a place that composer will be in the path
RUN curl -sS https://getcomposer.org/installer | php -d allow_url_fopen=On -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
