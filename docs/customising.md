# Customising for your convention

Concourse comes from the portal used by the Glasgow 2024 Worldcon. The
Worldcon specific parts have been replaced with placeholders, which need
replacing by hand before it is used for another convention. Most of them use
`example.com`, so searching for it finds them:

```
git grep -n example.com -- site
```

The matches in `scripts/db-smoke.php`, `scripts/mock-oidc.php` and the
compose files are test data and examples, and can be left alone.

## config.php

In `site/lib/config.php`:

- `CON_NAME`, `CON_SHORT_NAME`, `CON_WEBSITE`, `REGISTRATION_URL`,
  `CON_START_DATE` and `TIMEZONE`.
- The contact addresses: `SUPPORT_EMAIL`, `REGISTRATION_EMAIL`,
  `TECH_EMAIL` and `PROGRAMME_EMAIL`.
- `DISCORD_GUILD_ID`, `DISCORD_INVITE_CHANNEL_ID`, `DISCORD_API_CHANNEL_ID`
  and `DISCORD_SERVICE_ACCOUNTS`, which still hold Glasgow 2024's ids. Replace
  them with your own server's, or empty them if you don't use Discord. See
  [Discord](integrations/discord.md).
- The programme handlers, which default to Zoom Events. See
  [Programme handlers](integrations/programme.md#programme-handlers).
- `CONCLAR_VALID_APP_IDS`, if you use [ConClár](integrations/conclar.md).

See [config.php](configuration.md#configphp) for the full list.

## Pages

These pages have convention specific text or links in them:

| File | What to change |
|---|---|
| `site/web/index.php` | The home page cards, many of which link to `example.com`; the quick links shown by default. See [Cards](cards.md) |
| `site/web/login.php` | The membership purchase link and who can log in |
| `site/web/help.php` | The FAQ and Code of Conduct links, and the list of contact addresses |
| `site/web/programme-guide.php` | Where the programme guide redirects to, and the holding page shown before it opens |
| `site/web/newsletter.php` | Where the newsletter redirects to, and the holding page |
| `site/web/convention-guide.php` | The convention guide holding page |
| `site/web/maps.php`, `site/web/times.php` | Maps and opening times |
| `site/web/upload-photo.php`, `site/web/upload-presentation.php` | The upload forms they link to |
| `site/web/virtual-gatherings.php` | The request form it links to, and the mention of RingCentral Events |
| `site/web/guides/` | Participant and moderator guides |
| `site/web/privacy-policy.php`, `site/web/attributions.php` | The privacy policy and credits |
| `site/web/502.html` | The maintenance page |
| `site/lib/templates/email.html.twig` | The layout of emails the portal sends |

Several cards and pages name the platform used at Glasgow 2024 (for example
RingCentral Events or Planorama). Change these to whatever your convention
uses, or remove the cards.

## Look and feel

- The logo is `site/web/resources/logo-*.webp` and the browser icon is
  `site/web/resources/favicon.png`.
- Colours and fonts are in `site/web/resources/style.css`; the colours are
  custom properties at the top of the file.
- Card images are the SVGs in `site/web/resources/`. See [Cards](cards.md).
- The images in `site/web/resources/help/` and `site/web/resources/discord/`
  are screenshots from Glasgow 2024.

## Roles and permissions

Nobody can manage roles until the first administrator has been set up in the
database; see [The first administrator](roles-and-permissions.md#the-first-administrator).

The migrations create roles named for Glasgow 2024's membership types. Decide
which roles your convention needs and what each can see, then set them up
under [Manage roles](roles-and-permissions.md#managing-roles).

## Deployment

- In `deploy/.env.prod.example` and `deploy/.env.staging.example`, replace
  `example/concourse` with your GitHub repository.
- Set up the secrets file for the integrations you use. See
  [The secrets file](configuration.md#the-secrets-file).
