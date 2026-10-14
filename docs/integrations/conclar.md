# ConClár

[ConClár](https://github.com/lostcarpark/conclar) is a programme guide. Using
its sync feature, the portal can store members' starred items so they follow
them across devices. Both sides need configuring.

## The portal

Add the origin ConClár is served from to the secrets file:

```ini
[conclar]
allowed_origins[] = https://guide.example.com
; Optional. Adds item titles to the favourites report
schedule_url = https://guide.example.com/programme.json
```

Allowed origins are also valid places to send members back to after logging
in. No trailing slash.

Each ConClár instance identifies itself with an `app_id`. Add the ones you
use to `CONCLAR_VALID_APP_IDS` in `site/lib/config.php`; requests with any
other `app_id` are rejected. `CONCLAR_MAX_ITEMS` caps how many items a member
can store per app id.

The API is `GET` and `PATCH /api/conclar/apps/<app_id>/selections` and
`GET /api/conclar/profile`. For the session cookie to be sent from another
origin the portal must be served over HTTPS, and if ConClár is on a
different subdomain, `cookie_domain` must cover both (e.g. `.example.com`).

## ConClár

In ConClár's `config.json`, set `APP_ID` to one of the ids in
`CONCLAR_VALID_APP_IDS`, and add a `SYNC` section with `API_URL` pointing at
the portal's ConClár API:

```json
{
  "APP_ID": "example2026",
  "SYNC": {
    "API_URL": "https://portal.example.com/api/conclar"
  }
}
```

`API_URL` has no trailing slash. The `SYNC` section also sets the labels for
logging in and out and the popup prompting members to log in; see the
`SYNC.*` settings in ConClár's
[configuration reference](https://github.com/lostcarpark/conclar/blob/main/docs/config_reference.md),
and [Syncing selections across devices](https://github.com/lostcarpark/conclar#syncing-selections-across-devices)
in its README. The API ConClár expects is described in its
[sync server API](https://github.com/lostcarpark/conclar/blob/main/docs/sync_server_api.md).

For the favourites report, `schedule_url` is the programme data ConClár
reads: `DATA_URLS.SCHEDULE` or `DATA_URLS.COMBINED` in its `config.json`.
The report needs a file with a `schedule` array, as described in ConClár's
[file specs](https://github.com/lostcarpark/conclar/blob/main/docs/conclar_file_specs.md),
so the older bare array format from `PROGRAM_DATA_URL` won't work.

## Reports and the guide card

Members with the `see-programme-reports` permission get a Programme reports
card with the most starred items, split by the `online` and `in-person` roles.

To make the Programme guide card go straight to ConClár, give members the
`see-guide-redirect` permission and set the URL in `site/web/index.php` and
`site/web/programme-guide.php`.
