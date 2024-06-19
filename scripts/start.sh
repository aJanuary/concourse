#!/bin/sh
#
# Simple start script that ensures that the migrations are run
# then starts the apache server
# 

# Wait for the database to be ready
until mysqlcheck -h $CONFIG_DB_HOST -u $CONFIG_DB_USER --password=`cat $CONFIG_DB_PASSWORD_FILE` --databases members; do
  echo "waiting for mysql..."
  sleep 5
done

# Setup and install vendor libs
cd /srv/lib
composer install

# Setup and run migrations
cd /var/php-migrations
# ensure phinx etc is installed
composer install

# run the migrations
# NOTE: RUNTIME_ENV is set to developmen or production ...
vendor/bin/phinx migrate -e $RUNTIME_ENV

# Then start the server
cd
apache2-foreground
