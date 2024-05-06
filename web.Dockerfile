FROM php:8.2-apache
# Need zip and client for migrations and start script
RUN apt-get update -y && apt-get install -y libmariadb-dev zip mariadb-client
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

WORKDIR /var/www/html
