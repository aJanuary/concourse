#!/bin/sh
#

# Wait for the database to be ready
until mysqlcheck -h db -u dba --password=`cat $CONFIG_DB_PASSWORD_FILE` --databases members; do
  echo "waiting for mysql..."
  sleep 5
done

# Setup and run migrations
cd /var/php-migrations
curl -s https://getcomposer.org/installer | php -d allow_url_fopen=On
php composer.phar require robmorgan/phinx
vendor/bin/phinx migrate -e development

# Then start the server
cd
apache2-foreground
