#
#
until mysqlcheck -h db -u dba --password=Rh1annon --databases members; do
  echo "waiting for mysql..."
  sleep 5
done

#
cd /var/php-migrations
vendor/bin/phinx migrate -e development
# to rollback
# vendor/bin/phinx rollback -e development 

cd
apache2-foreground

# To allow for database migrations ...
# 
# RUN curl -s https://getcomposer.org/installer | php -d allow_url_fopen=On
# RUN php composer.phar require robmorgan/phinx

#  mysql -h db -u dba --password=Rh1annon -e "SHOW VARIABLES LIKE 'version%'"

# mysqlcheck -h db -u dba --password=Rh1annon --databases members
# if [ $? -eq 0 ] 
# failed
# else
# passed
# fi
