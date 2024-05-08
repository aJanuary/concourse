#!/bin/sh
#
# Simple start script that ensures that the migrations are run
# then starts the apache server
# 

# Wait for the database to be ready
until mysqlcheck -h db -u dba --password=`cat $CONFIG_DB_PASSWORD_FILE` --databases members; do
  echo "waiting for mysql..."
  sleep 5
done

# Setup and run migrations
cd /var/php-migrations
# ensure phinx etc is installed
composer update
# run the migrations
vendor/bin/phinx migrate -e development

# Then start the server
cd
apache2-foreground
