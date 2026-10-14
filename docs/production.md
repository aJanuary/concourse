# Production

Requirements on the host:

- [Docker Compose](https://docs.docker.com/compose/)
- A web server to proxy to the container and terminate HTTPS (such as
  [Caddy](https://caddyserver.com/))

Before deploying, work through [Customising for your convention](customising.md);
the images contain the code, so changes to it need new images.

## Directory structure on the host

The files in the `deploy` directory are a working example of a deployment.
They assume this layout on the host:

```
/opt
|__ portal
    |__ app
    |__ analytics
    |__ secrets
```

## Secrets files

The secrets files go in `/opt/portal/secrets`. There are three:

- `db_root_password.txt`: a single line holding the database root password
- `db_dba_password.txt`: a single line holding the password for the
  application's database user
- `web_secrets.ini`: the settings and keys for logins and third party
  services. See [The secrets file](configuration.md#the-secrets-file)

Generate the passwords with, for example, `openssl rand -hex 24`.

## Database

The portal runs on MySQL by default. To run on Postgres instead, uncomment
`COMPOSE_FILE` in the env file. `docker-compose.postgres.yml` swaps the `db`
service for `postgres:17` and keeps its data in a `portal-pgdata` volume.
Switching an existing install between databases does not carry the data over.

The compose files set the `web` container's database settings, which the
migrations and the start script also use:

| Variable | Purpose |
|---|---|
| `CONFIG_DB_DRIVER` | `mysql` (default, MySQL 8, port 3306) or `pgsql` (Postgres, port 5432) |
| `CONFIG_DB_HOST`, `CONFIG_DB_NAME`, `CONFIG_DB_USER` | Where the database is and who to connect as |
| `CONFIG_DB_PASSWORD_FILE` | A file holding the password |
| `CONFIG_DB_PORT` | Overrides the default port |

## Deploying

1. Publish the images by running the "Build and publish docker images"
   GitHub Action (see [Images](#images)).
2. Copy the files from the `deploy` directory to `/opt/portal/app`, including
   the `.env.*.example` files.
3. Copy `.env.prod.example` to `.env.prod` and set `WEB_IMAGE` and
   `NGINX_IMAGE` to your repository's images.
4. Create the secrets files in `/opt/portal/secrets` (see
   [Secrets files](#secrets-files)) and an empty `/opt/portal/analytics`.
5. Create the database volume: `docker volume create portal-mysqldata`, or
   `portal-pgdata` on Postgres (see [Database](#database)).

To start the server run `/opt/portal/app/redeploy.sh`. This pulls the images
named in `.env.prod` and restarts the containers on them, running any new
migrations. The portal is then available on `127.0.0.1:8080` for the proxy.
The database is also available on `127.0.0.1:53306` (`55432` on Postgres)
if needed.

The settings an env file can have are listed in `.env.prod.example`.

The database starts with no administrator, so nobody can grant permissions
and the home page is empty. Set one up as described in
[The first administrator](roles-and-permissions.md#the-first-administrator).

### The proxy

The portal works out its own URL from the request, using
`X-Forwarded-Proto` and `X-Forwarded-Host` when the proxy sets them, so links
it generates (such as OAuth redirect URIs) use the public address. Caddy sets
these by default:

```
portal.example.com {
  reverse_proxy 127.0.0.1:8080
}
```

Set `client_ip_header` in the `[auth]` section of the secrets file to the
header the proxy puts the client address in, e.g. `HTTP_X_FORWARDED_FOR`.
Otherwise every visitor appears to come from the proxy and shares its login
rate limits.

## Images

The "Build and publish docker images" workflow is run by hand from the
Actions tab. It publishes `ghcr.io/<owner>/<repository>-web` and `-nginx`:

- Run on the default branch, the images are tagged `latest`, which
  `.env.prod.example` follows.
- Run on any other branch, they are tagged with the branch name in lower
  case, with characters other than letters, digits, `.`, `_` and `-` changed
  to `-`. For example `feature/SSO` becomes `feature-sso`.

Each run also tags the images with the branch and commit, e.g.
`main-1a2b3c4`, so a deployment can be pinned to it.

## Staging

A staging instance can run on the same host, following a branch's images.
Copy `.env.staging.example` to `.env.staging` and set the image tags to the
branch. It gives staging its own compose project name, data directory
(`/opt/portal-staging`, laid out like `/opt/portal`), database volume and
ports; create the directory, secrets and volume as for production. Use a
different `cookie_name` in its `web_secrets.ini` if it shares a parent domain
with production. Then:

```bash
# Production
/opt/portal/app/redeploy.sh .env.prod

# Staging
/opt/portal/app/redeploy.sh .env.staging
```

## Analytics

Page views, card clicks and quick link changes are appended to hourly log
files in `/opt/portal/analytics`, one event per line:

```
2026-08-08T14:03:12 in-person card-click maps
```

The second field is the kind of member: `in-person`, `online`, `anonymous`,
`not-logged-in` or `unknown`.
