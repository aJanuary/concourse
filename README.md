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
    |__ analytics
    |__ secrets
```

To deploy

1. Publish the images by running the "Build and publish docker images"
   GitHub Action (see [Images](#images)).
2. Copy the files from the `deploy` directory to `/opt/portal/app`, including
   the `.env.*.example` files.
3. Copy `.env.prod.example` to `.env.prod` and set `WEB_IMAGE` and
   `NGINX_IMAGE` to your repository's images.
4. Create the secrets files in `/opt/portal/secrets` (see
   [Secrets files](#secrets-files)) and an empty `/opt/portal/analytics`.
5. Create the database volume: `docker volume create portal-mysqldata`.

To start the server run `/opt/portal/app/redeploy.sh`. This pulls the images
named in `.env.prod` and restarts the containers on them, running any new
migrations. The portal is then available on `127.0.0.1:8080` for the proxy.
The MySQL database is also available on `127.0.0.1:53306` if needed.

To run on Postgres instead, uncomment `COMPOSE_FILE` in the env file and
create a `portal-pgdata` volume instead. Its database is on port `55432`.

#### Images

The "Build and publish docker images" workflow is run by hand from the
Actions tab. It publishes `ghcr.io/<owner>/<repository>-web` and `-nginx`:

- Run on the default branch, the images are tagged `latest`, which
  `.env.prod.example` follows.
- Run on any other branch, they are tagged with the branch name in lower
  case, with characters other than letters, digits, `.`, `_` and `-` changed
  to `-`. For example `feature/SSO` becomes `feature-sso`.

Each run also tags the images with the branch and commit, e.g.
`main-1a2b3c4`, so a deployment can be pinned to it.

#### Staging

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

The settings an env file can have are listed in `.env.prod.example`.

### Secrets files

When you deploy the application as above the secrets files should be placed in `/opt/portal/secrets`. There are three files

- db_root_password.txt: file with single line containing a string to use for the DB root password
- db_dba_password.txt: file with single line containing a string to use for the DB DBA/Application password
- web_secrets.ini: file containing the tokens and keys for the application for 3rd party apps (such as Zoom Events, Discord, SMTP etc)

#### web_secrets.ini

All sections are optional; a feature whose section is missing reports which key is needed when it is used.

| Section | Keys | Purpose |
|---|---|---|
| `[discord]` | `bot_token`, `client_id`, `client_secret` | Discord bot and OAuth app |
| `[api_keys]` | `<client name> = <shared secret>` | Shared secrets for API clients |
| `[zoom_events]` | `client_id`, `client_secret`, `account_id`, `event_id`, `ticket_type_id`, `ticket_email_domain` | The `zoomevents` programme handler; see [Programme handlers](#programme-handlers). `ticket_email_domain` is optional |
| `[auth]` | `allowlist[]`, `providers[]`, `client_ip_header` | Emails allowed to log in (empty means everyone); enabled login providers (default `dev`, which only works when `RUNTIME_ENV=development`; `local` is email login); the `$_SERVER` key holding the client address behind a proxy, e.g. `HTTP_X_FORWARDED_FOR` (default `REMOTE_ADDR`) |
| `[oidc]` | `provider_url`, `client_id`, `client_secret`, `scopes[]`, `claims[...]`, `role_map[...]`, `provider_logout`, `password_change_url`, `verify_tls` | The `oidc` login provider; see [OpenID Connect login](#openid-connect-login) |
| `[smtp]` | `host`, `port`, `username`, `password`, `secure` | Outgoing mail for the `local` login provider. `secure` is empty, `tls` or `ssl` |
| `[crypto]` | `signing_key` | Signs emailed links and hashes login codes for the `local` provider. At least 32 characters; generate with `openssl rand -hex 32` |
| `[conclar]` | `allowed_origins[]`, `schedule_url` | Origins hosting the ConClár programme guide, which may call `/api/conclar/` with the session cookie and are also valid login redirect targets (no trailing slash); optional programme JSON used to show item titles in the favourites report. See [ConClár](#conclár) |
| `[cookie]` | `cookie_name` (default `session`), `cookie_domain` (default empty) | Session cookie name and domain |

### Programme handlers

A programme item's join link (`/deep-link/session?item_id=...`) sends the member
to whichever platform the item uses. A *handler* decides where, so a convention
picks the platforms it uses rather than the portal assuming one.

| Handler | Destination of a session | Replays |
| ------- | ------------------------ | ------- |
| `zoomevents` | Zoom Events session id. The member gets a ticket for the event, created through the API the first time they join, and is sent to the session | Zoom Events session id |
| `stream` | Room id. The room's current URL is managed under *Manage stream URLs*, so it can change without touching sessions | A YouTube URL is embedded without cookies; any other URL is redirected to |
| `discord` | Not used. Goes to `/deep-link/chat` for the item's Discord post | Not supported |

Settings in `site/lib/config.php`:

- `DEFAULT_PROG_HANDLER`: used by sessions that don't name a handler.
- `AVAILABLE_PROG_HANDLERS`: the handlers the admin pages and the sessions API accept.
- `PROG_REPLAY_HANDLER`: the handler that opens replays.
- `PROG_REPLAY_REQUIRES_LOGIN`: whether replays need a login. Joining a live item always does.
- `PROG_JOIN_PERMISSION`: the permission needed to join or replay, or empty for any logged in member.
- `PROG_TEST_MODE`: while true, join links show a "you're early" page.

The other links work the same way: `/deep-link/participate` takes an
`item_id` (a session) or a `room_id` (a stage), `/deep-link/stage` redirects to
a stage's viewer URL, and `/deep-link/stream?room_id=...` goes straight to a
room's stream. `/stream` lists the rooms that are streaming.

For `zoomevents`, create a Server-to-Server OAuth app in Zoom and set
`client_id`, `client_secret` and `account_id` in `[zoom_events]`, plus the
`event_id` and the `ticket_type_id` that tickets are created under. Tickets are
registered as `zoomlogin+<badge number>@<ticket_email_domain>` so members' own
addresses aren't shared with Zoom; leave `ticket_email_domain` out to use the
member's email.

#### Adding a handler

The deep-link pages never mention a platform, so a new handler doesn't touch
them.

1. Implement `ProgHandler` in `site/lib/prog_handlers/`. `join()` is given the
   item (`kind` is `session` or `replay`, plus `item_id`, `title`,
   `destination` and the logged in `member`) and returns a
   `ProgHandlerResult`: `redirect`, `message`, `embed` or `login_required`.
2. Add it to `prog_handler_factories()` in `site/lib/prog_handlers.php`.
3. Add its name to `AVAILABLE_PROG_HANDLERS`.

#### Sessions API

A programme tool can push sessions with signed requests to
`/api/upsert-sessions` (a list of `{itemId, title, destination, handler}`) and
`/api/delete-sessions` (a list of item ids). Clients are the entries in
`[api_keys]`. `test/api-tester/send_api_request.py` signs and sends a request:

```
test/api-tester/send_api_request.py upsert-sessions '[{"itemId": "1", "title": "Panel", "destination": "room-a", "handler": "stream"}]' --client <client name>
```

### ConClár

ConClár can store members' starred items on the portal so they follow them
across devices. Add the origin it is served from to the secrets file:

```ini
[conclar]
allowed_origins[] = https://guide.example.com
; Optional. Adds item titles to the favourites report
schedule_url = https://guide.example.com/programme.json
```

Each ConClár instance identifies itself with an `app_id`. Add the ones you
use to `CONCLAR_VALID_APP_IDS` in `site/lib/config.php`; requests with any
other `app_id` are rejected. `CONCLAR_MAX_ITEMS` caps how many items a member
can store per app id.

The API is `GET` and `PATCH /api/conclar/selections?app_id=...` and
`GET /api/conclar/profile`. For the session cookie to be sent from another
origin the portal must be served over HTTPS (see `cookie_domain` above).

Members with the `see-programme-reports` permission get a Programme reports
card with the most starred items, split by the `online` and `in-person` roles.

`docker-compose.yml` has the same list with a description of each key.

### Sign-ups

The Sign-ups page (`/sign-ups`) lists links to sign-up forms for items with
limited space. Members with `manage-programme` add them under Manage programme,
one at a time or by pasting CSV. A bulk add with any invalid row imports
nothing.

Entries are grouped by their free-text category, then by session start time.
Each has an optional opening and closing time; before it opens the page shows
when it will, and after it closes the link is replaced with "Closed". The links
are only shown to members with the `see-signup-links` permission.

To fill in member details, put `{name}`, `{email}` or `{badge_no}` anywhere in
the sign-up URL, for example
`https://forms.example.com/f?entry.1={name}&entry.2={badge_no}`. For someone
not logged in, query parameters holding a placeholder are removed,
and placeholders elsewhere in the URL are left empty.

### Roles

Members with `manage-roles` choose which permissions each role has under
Manage roles, and give or remove roles for one member at a time from "Edit a
member's roles" there. Members are found by badge number, name or email in
the `members` table, so a member usually has to have logged in once before
they can be given a role. Changes apply from the member's next page load.

Logging in can undo a change made by hand. A role in the OIDC `role_map` is
set from the provider's claims at every login, and a role listed for a badge in
`DEFAULT_ROLES_BY_BADGE` is given back at every login. The page marks these
roles. You cannot remove a role from yourself if that would take away your own
`manage-roles`.

## Development

### Running locally

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
Existing secrets are left alone, so run it again to switch database.

The choice of database is kept as `COMPOSE_FILE` in `.env`, so plain
`docker compose` commands (`up -d`, `down`, `logs web`, `exec web ...`) work
on the same set of containers afterwards. A `docker-compose.override.yml` is
included automatically. To add `docker-compose.oidc.yml`, append it to
`COMPOSE_FILE`.

| Script | Does |
|---|---|
| `scripts/open-portal` | Opens the portal |
| `scripts/open-mailpit` | Opens [mailpit](https://mailpit.axllent.org/), which catches all mail the portal sends |
| `scripts/open-db-admin` | Opens phpMyAdmin, or pgAdmin on Postgres (log in to the `portal` server with the password in `secrets/db_dba_password.txt`) |
| `scripts/db-shell` | A `mysql` or `psql` shell on the database. Arguments go to the client; `scripts/db-shell < dump.sql` runs a file |

Only the portal has a fixed port. The database, mailpit and the admin tools
get a free port each time they start, which the `open-*` scripts look up
(`docker compose port <service> <port>` shows it too). Nothing has a fixed
container name and the database volume belongs to the compose project, which
is named after the checkout's directory. So two checkouts can run at once:
set `PORTAL_PORT=8081` in the second one's `.env`.

The database volume used to be an external volume called
`portal-mysqldata`. To keep using it, add this to
`docker-compose.override.yml`:

```yaml
volumes:
  portal-mysqldata:
    name: portal-mysqldata
    external: true
```

### Email login

The `local` provider lets a member enter their email address and then either
a password or a six digit code sent to that address. Enable it with
`providers[] = local` in the `[auth]` section, and set `[smtp]` and
`[crypto]`. For development, [mailpit](https://mailpit.axllent.org/) is a
convenient SMTP server that shows what was sent. The development environment
runs it; see [Running locally](#running-locally).

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

Set `password_change_url` to the provider's password change page. The profile
page links to it for members who signed in through the provider, since the
portal has no password for them. Without it the profile page shows no link.

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

To develop against Postgres, run `scripts/bootstrap pgsql`. This layers
`docker-compose.postgres.yml` over the default compose file, which swaps the
`db` service for `postgres:17`, keeps its data in a separate `portal-pgdata`
volume, and replaces phpMyAdmin with pgAdmin.

Switching an existing install between databases does not carry the data over.

Any SQL that differs between the two databases goes through the helpers in
`site/lib/db_dialect.php`; write everything else in the SQL the two have in
common. In particular, MySQL compares text case-insensitively and Postgres does
not, so compare emails with `LOWER(email) = LOWER(?)`.

`scripts/db-smoke.php` runs every `db_*` function against the configured
database and cleans up after itself. Run it in the `web` container after the
migrations have been applied: `docker compose exec web php /var/scripts/db-smoke.php`.

The "Check" GitHub Action runs `scripts/bootstrap` on each database, which
applies every migration to an empty database, then this smoke test. It runs
on pushes to `main` and on pull requests.

### Updating dependencies

1. Add the dependency to `site/lib/composer.json`
2. Run `docker compose exec web bash -c 'cd /srv/lib; composer update'`

### Database changes

1. All migrations use phinx for database changes. The migrations are in the folder
   `sites/php-migrations`
   To create a migration file run (replace MIGRATIONNAME with a meaningful name for
   your migration)
   `docker compose exec web bash -c 'cd /var/php-migrations; vendor/bin/phinx create MIGRATIONNAME'`
2. Edit the file to put in the PHP or SQL for the migration.
3. The migration will be automatically applied to the database on start up of the web container.
   If you want to apply the change right away do:
   ```
   docker compose exec web bash -c 'cd /var/php-migrations; vendor/bin/phinx migrate -e development'
   ```

See the Phinx documentation for more details [https://book.cakephp.org/phinx/0/en/index.html]
