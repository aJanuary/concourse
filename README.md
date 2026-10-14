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
- web_secrets.ini: file containing the tokens and keys for the application for 3rd party apps (such as RCE, Discord etc)

#### web_secrets.ini

All sections are optional; a feature whose section is missing reports which key is needed when it is used.

| Section | Keys | Purpose |
|---|---|---|
| `[discord]` | `bot_token`, `client_id`, `client_secret` | Discord bot and OAuth app |
| `[api_keys]` | `<client name> = <shared secret>` | Shared secrets for API clients |
| `[rce]` | `api_key`, `ticket_id` | RingCentral Events magic links |
| `[auth]` | `allowlist[]`, `providers[]` | Emails allowed to log in (empty means everyone); enabled login providers (default `dev`, which only works when `RUNTIME_ENV=development`) |
| `[cookie]` | `cookie_name` (default `session`), `cookie_domain` (default empty) | Session cookie name and domain |

`docker-compose.yml` has the same list with a description of each key.

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

### Database

The portal runs on MySQL by default. Set `CONFIG_DB_DRIVER` on the `web`
container to choose the database:

| `CONFIG_DB_DRIVER` | Database | Default port |
| ------------------ | -------- | ------------ |
| `mysql` (default)  | MySQL 8  | 3306         |
| `pgsql`            | Postgres | 5432         |

`CONFIG_DB_HOST`, `CONFIG_DB_NAME`, `CONFIG_DB_USER` and
`CONFIG_DB_PASSWORD_FILE` apply to both. `CONFIG_DB_PORT` overrides the default
port. The migrations and the start script pick up the same settings.

To develop against Postgres, layer `docker-compose.postgres.yml` over the
default compose file. It swaps the `db` service for `postgres:17`, keeps its
data in a separate `portal-pgdata` volume (no need to create it), and turns off
phpMyAdmin:

```
docker-compose -p portal-dev -f docker-compose.yml -f docker-compose.postgres.yml up
```

Switching an existing install between databases does not carry the data over.

Any SQL that differs between the two databases goes through the helpers in
`site/lib/db_dialect.php`; write everything else in the SQL the two have in
common. In particular, MySQL compares text case-insensitively and Postgres does
not, so compare emails with `LOWER(email) = LOWER(?)`.

`scripts/db-smoke.php` runs every `db_*` function against the configured
database and cleans up after itself. Run it in the `web` container after the
migrations have been applied: `docker exec web php /var/scripts/db-smoke.php`.

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
