#!/bin/sh
#
# Simple start script that ensures that the migrations are run
# then starts the apache server
# 

# Wait for the database to be ready
if [ "${CONFIG_DB_DRIVER:-mysql}" = "pgsql" ]; then
  until pg_isready -h $CONFIG_DB_HOST -p ${CONFIG_DB_PORT:-5432} -U $CONFIG_DB_USER -d $CONFIG_DB_NAME; do
    echo "waiting for postgres..."
    sleep 5
  done
else
  until mariadb-check --skip-ssl -h $CONFIG_DB_HOST -u $CONFIG_DB_USER --password=`cat $CONFIG_DB_PASSWORD_FILE` --databases members; do
    echo "waiting for mysql..."
    sleep 5
  done
fi

# Setup and install vendor libs
cd /srv/lib
composer install

# Setup and run migrations
cd /var/php-migrations
# ensure phinx etc is installed
composer install

# run the migrations
# NOTE: RUNTIME_ENV is set to development or production ...
vendor/bin/phinx migrate -e $RUNTIME_ENV

# Correct permissions on analytics folder
chown -R www-data:www-data $CONFIG_ANALYTICS_DIR

# Then start the server
cd
php-fpm