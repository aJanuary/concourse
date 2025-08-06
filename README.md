# Member portal

This is a member portal for use by conventions.

The design focusses around the at-con experience, aiming to give members access to the things they will need at-con
quickly and easily.

It also acts as glue between the registration system and third party services. Because members have to log in,
the member portal can generate links that have a unique token, or embeds their username or email.

The code is based on what was used for the Glasgow 2024 Worldcon, but with the Worldcon specific parts removed.
It can't be used as-is, but can be used as a basis on which to build your con specific portal experience.
Start by looking for and replacing references to example.com.

Requirements:

- [https://docs.docker.com/compose/](docker-compose)
- A webserver to be proxy to the container (such as caddy)

### Deployment and Directory Structure on Host

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

NOTE: the MySQL database for the portal will also be available on `53306` if needed (only accessible from the host machine).

### Secrets files

When you deploy the applicaton as above the secrets files should places in `/opt/portal/secrets`. There are three files

- db_root_password.txt: file with single line containing a string to use for the DB root password
- db_dba_password.txt: file with single line containing a string to use for the DB DBA/Application password
- web_secrets.ini: file containing the tokens and keys for the application for 3rd party apps (such as RCE, Discord, OAuth etc)

## Development

### Deployment for Development

Requirements:

- [https://docs.docker.com/compose/](docker-compose)

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
   To create a migration file run (replace MIGRATIONNAME with a meaningful name for
   your migration)
   `docker exec web bash -c 'cd /var/php-migrations; vendor/bin/phinx create MIGRATIONNAME'`
2. Edit the file to put in the PHP or SQL for the migration.
3. The migration will be automatically applied to the database on start up of the web container.
   If you want to apply the change right away do:
   ```
   docker exec web bash -c 'cd /var/php-migrations; vendor/bin/phinx migrate -e development'
   ```

See the Phinx documentation for more details [https://book.cakephp.org/phinx/0/en/index.html]
