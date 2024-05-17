# Members page for Glasgow 2024

## Development

### Deployment for Development
Requirements:
* [https://docs.docker.com/compose/](docker-compose)

1. Clone the repository
2. Create a folder called `secrets` and populate it with the secrets.
   See the `secrets` section of the `docker-compose.yml` file for an explanation
   of what secrets should be placed in what file.
3. Create a docker volume for the database
  `docker volume create --name=portal-mysqldata`
4. Run docket compose to start the servers:
  ```
  docker-compose -p portal-dev -f docker-compose.yml up
  ```
  NOTE: the -p portal dev is optional but gives a way to group the containers if
  you have other projects on your machine.

5. Go to [http://localhost:8080] for the portal, and [http://localhost:8081] for
   phpMyAdmin.

### Database changes
1. All migrations use phinx for database changes. The migrations are in the folder
   `sites/php-migrations`
   To create a migration file run (replace MIGRATIONNAME with a meanningful name for
   your migration)
   `docker exec web bash -c 'cd /var/php-migrations; vendor/bin/phinx create MIGRATIONNAME'`
2. Edit the file to put in the PHP or SQL for the migration.
3. The migration will be automatically applied to the database on start up of the web container.
   If you want to apply the change right away do:
   ```
   docker exec web bash -c 'cd /var/php-migrations; vendor/bin/phinx migrate -e development'
   ```

See the Phinx documentation for more details [https://book.cakephp.org/phinx/0/en/index.html]

### Login with Clyde

Add clyde credentials to the web_secrets.ini file. To get these you will need a valid
publically accesible URL for the portal. You can do this by using a servivce such
as loophole and issuing a command like:

```
loophole http 8080 --hostname MYPORTAL
```

Replace MYPORTAL with a name representing your instance. You will end up with a URL similar to
`https://MYPORTAL.loophole.site`. Then request credentials to from a clyde admin for staging the staging
site providing a callback as `https://MYPORTAL.loophole.site/clyde`.

