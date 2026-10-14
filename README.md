# Concourse

Concourse is a member portal for conventions.

The design focusses around the at-con experience, aiming to give members access to the things they will need at-con
quickly and easily.

It also acts as glue between the registration system and third party services. Because members have to log in,
the portal can generate links that have a unique token, or embeds their username or email.

The code is based on what was used for the Glasgow 2024 Worldcon, but with the Worldcon specific parts removed.
It can't be used as-is, but can be used as a basis on which to build your con specific portal experience.
Start with [Customising for your convention](docs/customising.md).

It is a PHP application run with [Docker Compose](https://docs.docker.com/compose/), on MySQL or Postgres.

## Quick start

With Docker Compose and `openssl` installed:

```
scripts/bootstrap
```

The portal is then at <http://localhost:8080>. The database starts empty, so
add yourself as a member and administrator as described in
[The first administrator](docs/roles-and-permissions.md#the-first-administrator).
See [Development](docs/development.md) for more.

## Documentation

- [Customising for your convention](docs/customising.md): the placeholders to replace by hand
- [Development](docs/development.md): running locally, migrations and dependencies
- [Production](docs/production.md): secrets, the database, deploying, images and staging
- [Configuration](docs/configuration.md): reference for the secrets file and `config.php`
- [Logging in](docs/logging-in.md): email and OpenID Connect logins, and registration systems
- [Roles and permissions](docs/roles-and-permissions.md): the first administrator and managing roles
- [Cards](docs/cards.md): adding cards and pages to the home page
- [Integrations](docs/integrations/README.md): programme platforms, Discord, ConClár and the API
