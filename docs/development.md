# Development

## Running locally

Requirements:

- [Docker Compose](https://docs.docker.com/compose/)
- `openssl`, to generate the secrets

Clone the repository and run one of:

```
scripts/bootstrap          # MySQL
scripts/bootstrap pgsql    # Postgres
```

This creates any missing files in `secrets/` (random database passwords and
a starter `web_secrets.ini` with the `dev` and `local` login providers and
mail going to mailpit), builds the images, starts everything and waits for
the migrations to finish. The portal is then at <http://localhost:8080>.
Existing secrets are left alone, so run it again to switch database. See
[Database](production.md#database) for how the database is chosen.

The database starts empty: there are no members to log in as, and no one can
grant permissions, so the home page is empty. Add yourself as a member and
administrator as described in
[The first administrator](roles-and-permissions.md#the-first-administrator).

The `dev` login provider lets you log in as any existing member. It only
works when `RUNTIME_ENV=development`, which the development compose file sets.

The choice of database is kept as `COMPOSE_FILE` in `.env`, so plain
`docker compose` commands (`up -d`, `down`, `logs web`, `exec web ...`) work
on the same set of containers afterwards. A `docker-compose.override.yml` is
included automatically. To add `docker-compose.oidc.yml`, append it to
`COMPOSE_FILE`.

`site/lib`, `site/web` and `site/php-migrations` are mounted into the
container, so changes to them show on the next page load.

| Script | Does |
|---|---|
| `scripts/open-portal` | Opens the portal |
| `scripts/open-mailpit` | Opens [mailpit](https://mailpit.axllent.org/), which catches all mail the portal sends |
| `scripts/open-db-admin` | Opens phpMyAdmin, or pgAdmin on Postgres (log in to the `portal` server with the password in `secrets/db_dba_password.txt`) |
| `scripts/db-migrate` | Runs [Phinx](https://book.cakephp.org/phinx/0/en/index.html) in the `web` container: applies pending migrations, or runs the Phinx command given (`create`, `status`, `rollback`, ...) |
| `scripts/composer` | Runs Composer in the `web` container on `site/lib`; `-d /var/php-migrations` runs it on the migrations instead |
| `scripts/db-shell` | A `mysql` or `psql` shell on the database. Arguments go to the client; `scripts/db-shell < dump.sql` runs a file |

### Ports and running several checkouts

The portal is on port 8080 unless `PORTAL_PORT` in `.env` says otherwise;
bootstrap stops before starting anything if that port is in use. Set
`PORTAL_PORT=0` to have Docker pick a free port each time the portal starts.
The database, mailpit and the admin tools always get a free port, which the
`open-*` scripts look up (`docker compose port <service> <port>` shows it
too). Nothing has a fixed container name and the database volume belongs to
the compose project, which is named after the checkout's directory. So two
checkouts can run at once, as long as their `PORTAL_PORT`s differ.

The database volume used to be an external volume called
`portal-mysqldata`. To keep using it, add this to
`docker-compose.override.yml`:

```yaml
volumes:
  portal-mysqldata:
    name: portal-mysqldata
    external: true
```

### Developing against an OpenID Connect provider

`scripts/mock-oidc.php` is a minimal provider that approves every login.
`docker-compose.oidc.yml` runs it at `http://localhost:8090`; its header has
the matching `[oidc]` settings, and `secrets/mock-oidc/claims.json` sets who
the next login is. See [OpenID Connect login](logging-in.md#openid-connect-login).

### Debugging

The web image includes Xdebug, which is off unless `XDEBUG_MODE` is set. Set it (for example
`XDEBUG_MODE=debug`) when starting the containers to turn it on.

## Database changes

All database changes are [Phinx](https://book.cakephp.org/phinx/0/en/index.html)
migrations, in `site/php-migrations/db/migrations`. They are applied
automatically when the `web` container starts.

1. Create a migration, replacing `MigrationName` with a meaningful name:
   ```
   scripts/db-migrate create MigrationName
   ```
2. Edit the file to put in the PHP or SQL for the migration.
3. To apply it without restarting:
   ```
   scripts/db-migrate
   ```

`scripts/db-migrate status` lists which migrations have been applied, and
`scripts/db-migrate rollback` undoes the most recent one.

Migrations must work on both MySQL and Postgres. Any SQL that differs between
the two goes through the helpers in `site/lib/db_dialect.php`; write
everything else in the SQL the two have in common. In particular, MySQL
compares text case-insensitively and Postgres does not, so compare emails
with `LOWER(email) = LOWER(?)`.

## Checks

`scripts/db-smoke.php` runs every `db_*` function against the configured
database and cleans up after itself. Run it after the migrations have been
applied:

```
docker compose exec web php /var/scripts/db-smoke.php
```

The "Check" GitHub Action runs `scripts/bootstrap` on each database, which
applies every migration to an empty database, then this smoke test. It runs
on pushes to `main` and on pull requests.

## Updating dependencies

```
scripts/composer require vendor/package    # add one
scripts/composer update                    # update them all
```
