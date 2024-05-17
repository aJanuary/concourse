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
publically accessible URL for the portal. You can do this by using a service such
as loophole and issuing a command such as the following (where the string MYPORTAL is
going to be whatever unique hostname you want for your server):

```
loophole http 8080 --hostname MYPORTAL
```

Replace MYPORTAL with a name representing your server. It should not clash with any name that
other developers are using.
You will then end up with a URL similar to `https://MYPORTAL.loophole.site`.

Then request OAuth credentials from a clyde admin for the staging server. (You will be providing the Clyde
server a callback that looks like `https://MYPORTAL.loophole.site/clyde`).

FYI: information about loophole can be found at [https://loophole.cloud/]
