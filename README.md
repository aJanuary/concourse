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
- web_secrets.ini: file containing the tokens and keys for the application for 3rd party apps (such as RCE, Discord, SMTP etc)

#### web_secrets.ini

All sections are optional; a feature whose section is missing reports which key is needed when it is used.

| Section | Keys | Purpose |
|---|---|---|
| `[discord]` | `bot_token`, `client_id`, `client_secret` | Discord bot and OAuth app |
| `[api_keys]` | `<client name> = <shared secret>` | Shared secrets for API clients |
| `[rce]` | `api_key`, `ticket_id` | RingCentral Events magic links |
| `[auth]` | `allowlist[]`, `providers[]`, `client_ip_header` | Emails allowed to log in (empty means everyone); enabled login providers (default `dev`, which only works when `RUNTIME_ENV=development`; `local` is email login); the `$_SERVER` key holding the client address behind a proxy, e.g. `HTTP_X_FORWARDED_FOR` (default `REMOTE_ADDR`) |
| `[oidc]` | `provider_url`, `client_id`, `client_secret`, `scopes[]`, `claims[...]`, `role_map[...]`, `provider_logout`, `verify_tls` | The `oidc` login provider; see [OpenID Connect login](#openid-connect-login) |
| `[smtp]` | `host`, `port`, `username`, `password`, `secure` | Outgoing mail for the `local` login provider. `secure` is empty, `tls` or `ssl` |
| `[crypto]` | `signing_key` | Signs emailed links and hashes login codes for the `local` provider. At least 32 characters; generate with `openssl rand -hex 32` |
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

### Email login

The `local` provider lets a member enter their email address and then either
a password or a six digit code sent to that address. Enable it with
`providers[] = local` in the `[auth]` section, and set `[smtp]` and
`[crypto]`. For development, [mailpit](https://mailpit.axllent.org/) is a
convenient SMTP server that shows what was sent.

Members are found by email through a `MemberSource`
(`site/lib/auth/MemberSource.php`). The default reads the `members` table. To
look members up in a registration system, implement the interface and return
it from `auth_member_source()` in `site/lib/auth/providers.php`.

Several members can share an email address; they are offered a chooser after
logging in.

Code guesses, password guesses and emails sent are limited per email address
and per client address. Behind a proxy, set `client_ip_header` or every
visitor appears to share the proxy's address.

`DEFAULT_ROLES_BY_BADGE` in `site/lib/config.php` adds roles to members as
they log in with any provider. It maps role names to badge numbers, and
roles are only ever added:

```php
define('DEFAULT_ROLES_BY_BADGE', [
  'artist' => ['0042', '0043'],
]);
```

### OpenID Connect login

The `oidc` provider logs members in through any OpenID Connect identity
provider using the authorization code flow, and supports back-channel
single logout. Enable it with `providers[] = oidc` in the `[auth]` section.

Register the portal with the provider as a confidential client with:

- Redirect URI: `https://portal.example.com/oidc/callback`
- Back-channel logout URI: `https://portal.example.com/oidc/logout`. A logout
  token with a `sid` ends only the portal sessions started from that provider
  session; one with only a `sub` ends all of that user's portal sessions.

Example `[oidc]` section:

```ini
[oidc]
provider_url = https://idp.example.com/application/portal/
client_id = portal
client_secret = ...
scopes[] = email
scopes[] = profile
scopes[] = membership

; Which userinfo claim gives each member field
claims[badge_no] = member-number
claims[email] = email
claims[name] = display-name
claims[real_name] = full-name

; claim = role. The role is granted at login when the claim is truthy and
; removed when it is not.
role_map[is-attending] = attending
role_map[can-vote] = voter
```

`badge_no` is required. `email` and `name` default to the claims of those
names, and `real_name` is only stored when a claim is mapped for it. A login
without `badge_no`, `email` and `name` is refused.

A claim is truthy when it is `true`, a non-zero number, or one of the strings
`true`, `1`, `yes` or `on`. Several claims can map to the same role; the role
is granted if any of them is truthy. Only roles named in `role_map` are
removed, so roles assigned by hand are untouched. The roles must already
exist, and `DEFAULT_ROLES_BY_BADGE` still applies on top.

Logging out of the portal only ends the portal session by default. Set
`provider_logout = true` to also send the browser to the provider's
end-session endpoint (RP-initiated logout), passing the ID token from login
as the hint and returning to `https://portal.example.com/login`. Register
that URL as a post-logout redirect URI with the provider. The ID token is
kept with the session until it ends, and the provider must advertise an
`end_session_endpoint` in its discovery document.

Certificates are verified. For a development provider with a self-signed
certificate, set `verify_tls = false`.

For development without an identity provider, `scripts/mock-oidc.php` is a
minimal provider that approves every login. `docker-compose.oidc.yml` runs it
at `http://localhost:8090`; its header has the matching `[oidc]` settings, and
`secrets/mock-oidc/claims.json` sets who the next login is.

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
