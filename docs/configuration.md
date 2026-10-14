# Configuration

Configuration lives in two places:

- `web_secrets.ini`, the secrets file: keys for third party services and
  settings that differ between deployments. Changes take effect on the next
  page load.
- `site/lib/config.php`: settings for the convention as a whole. These are
  part of the code, so a change needs new images. See also
  [Customising for your convention](customising.md).

The database and the images to run are chosen by the containers' environment;
see [Production](production.md#database).

## The secrets file

All sections are optional; a feature whose section is missing reports which
key is needed when it is used. `scripts/bootstrap` writes a starter file for
development.

| Section | Keys | Purpose |
|---|---|---|
| `[auth]` | `allowlist[]`, `providers[]`, `client_ip_header` | Emails allowed to log in (empty means everyone); enabled login providers, in display order (see [Logging in](logging-in.md)); the `$_SERVER` key holding the client address behind a proxy, e.g. `HTTP_X_FORWARDED_FOR` (default `REMOTE_ADDR`) |
| `[oidc]` | `provider_url`, `client_id`, `client_secret`, `scopes[]`, `claims[...]`, `role_map[...]`, `provider_logout`, `password_change_url`, `verify_tls` | The `oidc` login provider; see [OpenID Connect login](logging-in.md#openid-connect-login) |
| `[smtp]` | `host`, `port`, `username`, `password`, `secure` | Outgoing mail for the `local` login provider. `secure` is empty, `tls` or `ssl` |
| `[crypto]` | `signing_key` | Signs emailed links and hashes login codes for the `local` provider. At least 32 characters; generate with `openssl rand -hex 32` |
| `[cookie]` | `cookie_name` (default `session`), `cookie_domain` (default empty) | Session cookie name and domain. Use a different name for each deployment that shares a parent domain |
| `[api_keys]` | `<client name> = <shared secret>` | Shared secrets for API clients; see [The API](integrations/api.md) |
| `[discord]` | `bot_token`, `client_id`, `client_secret`, `bm_invite` | Discord bot and OAuth app; see [Discord](integrations/discord.md). `bm_invite` is the invite code for the separate Business Meeting server; see [Business meeting](integrations/business-meeting.md#discord) |
| `[zoom_events]` | `client_id`, `client_secret`, `account_id`, `event_id`, `ticket_type_id`, `ticket_email_domain` | The `zoomevents` programme handler; see [Zoom Events](integrations/programme.md#zoom-events). `ticket_email_domain` is optional |
| `[conclar]` | `allowed_origins[]`, `schedule_url` | Origins hosting the ConClár programme guide, and an optional programme JSON for the favourites report. See [ConClár](integrations/conclar.md) |
| `[square]` | `access_token`, `environment`, `location_id`, `catalog_item_variation_id`, `webhook_signature_key`, `notification_url` | Square, for the site selection fee. `environment` is `sandbox` (default) or `production`; `notification_url` is optional. See [Site selection](integrations/site-selection.md#setting-up-square) |
| `[encryption]` | `config_encryption_key` | The key for [encrypted secrets](#encrypted-secrets), if it isn't given another way |

## Encrypted secrets

Keys and passwords in the secrets file can be encrypted, so that the file on
its own, shown on screen or copied somewhere it shouldn't be, doesn't give
them away. Anyone who also has the encryption key can still read them, as
can anyone who can run code in the `web` container, so keep the key
somewhere else if you can.

An encrypted value starts with `enc:`. Values without it are used as they
are, so you can encrypt some secrets and not others, and turn encryption on
one secret at a time. These can be encrypted: `[oidc] client_secret`,
`[smtp] password`, `[crypto] signing_key`, `[discord] bot_token` and
`client_secret`, `[zoom_events] client_secret`, `[square] access_token` and
`webhook_signature_key`, and every `[api_keys]` value.

1. Generate a key:
   ```
   scripts/encrypt-secret --generate-key
   ```
2. Give the `web` container the key in one of these ways. The first one set
   is used:
   - the `CONFIG_ENCRYPTION_KEY` environment variable;
   - a file named by `CONFIG_ENCRYPTION_KEY_FILE`. The compose files have
     commented lines for passing `secrets/config_encryption_key.txt` as a
     Docker secret, as the database passwords are;
   - `config_encryption_key` in the `[encryption]` section of the secrets
     file, in double quotes. This is the least protection, as the key is next
     to the values.
3. Encrypt each value. The script asks for the value without showing it, so
   it doesn't end up in your shell history, and encrypts it with the `web`
   container's key:
   ```
   scripts/encrypt-secret
   ```
   It can also read the value from a pipe. Paste the output, quotes and
   all, in place of the value in the secrets file:
   ```ini
   [smtp]
   password = "enc:3q2+7w..."
   ```

If an encrypted value can't be decrypted, because there is no key or the
wrong one, the feature that uses it fails with an error naming the setting.
The rest of the portal carries on.

To change the key, encrypt each `enc:` value again with the new key, by
running the script with `CONFIG_ENCRYPTION_KEY` set to the new key, then put
the new values and the new key in place together.

On a server without the checkout, run the same script from the image:

```
docker compose exec -e HTTP_HOST=localhost web php /var/scripts/encrypt-secret.php
```

## config.php

The settings in `site/lib/config.php`:

| Setting | Purpose |
|---|---|
| `CON_NAME`, `CON_SHORT_NAME` | The convention's names, shown in page titles and the header |
| `CON_WEBSITE`, `REGISTRATION_URL` | Links to the convention's website and registration |
| `CON_START_DATE` | The first day of the convention |
| `TIMEZONE` | The convention's timezone, used by the convention time card and page |
| `SUPPORT_EMAIL`, `REGISTRATION_EMAIL`, `TECH_EMAIL`, `PROGRAMME_EMAIL` | Contact addresses shown on pages and error messages |
| `ALLOW_ANONYMOUS_ACCESS` | Whether people can use public pages, such as the home page, without logging in. They get the permissions of the `default` role |
| `DEFAULT_ROLES_BY_BADGE` | Roles given at login by badge number; see [Roles and permissions](roles-and-permissions.md#roles-given-at-login) |
| `DISCORD_*` | See [Discord](integrations/discord.md) |
| `DEFAULT_PROG_HANDLER`, `AVAILABLE_PROG_HANDLERS`, `PROG_*` | See [Programme handlers](integrations/programme.md#programme-handlers) |
| `CONCLAR_VALID_APP_IDS`, `CONCLAR_MAX_ITEMS` | See [ConClár](integrations/conclar.md) |
| `BUSINESS_MEETING` | The business meeting page's platform, links, contact address, dates and MPC election; see [Business meeting](integrations/business-meeting.md#the-page) |
| `SITE_SELECTION` | The Worldcon being voted on, the fee, the bids, and links to the paper ballot, the election platform and how site selection works; see [Site selection](integrations/site-selection.md#the-page) |

The rest of the file reads the secrets file and the environment, and doesn't
need changing.
