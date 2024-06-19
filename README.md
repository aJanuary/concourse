# Members page for Glasgow 2024

## Releases

All release for production will be based on the `main` branch. First use create a releass,
all releases should be tagged following the pattern vNN.NN.NN when 'N' is a number. When a release
is published a github action should start to build the docker images and the release will
be available in the gitbub registry for this project and tagged.

## Deployment for Production
Requirements:
* [https://docs.docker.com/compose/](docker-compose)
* A webserver to be proxy to the container (such as caddy)

### Deployment and Directory Stucture on Host

The files in the `deploy` directory provide working examples for a deployment. Those files
assume the following directory structure on the host machine:

```
/opt
|__ portal
    |__ app
    |__ secrets
```

To deploy

1. Copy both the files from the `deploy` directory to `/opt/portal/app`
2. Create files for the secrets in `/opt/portal/secrest`

To start the server run `/opt/app/redeploy.sh`. This will pull the docker
containers and the application will be available on port `8080` for the web server.

NOTE: the MySQL database for the portal will also be available on `53306` if needed (only accesible from the host machine).

### Secrets files

When you deploy the applicaton as above the secrets files should places in `/opt/portal/secrets`. There are three files

- db_root_password.txt: file with single line containing a string to use for the DB root password
- db_dba_password.txt: file with single line containing a string to use for the DB DBA/Application password
- web_secrets.ini: file containing the tokens and keys for the application for 3rd party apps (such as RCE, Discord, Clyde etc)

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

### Updating dependencies
1. Add the dependency to `site/lib/composer.json`
2. Run `docker exec web bash -c 'cd /srv/lib; composer update'`

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
