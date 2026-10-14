# Programme

The portal sends members to programme items on whichever platform each item
uses, and lists sign-up forms for items with limited space.

## Programme handlers

A programme item's join link (`/deep-link/session?item_id=...`) sends the member
to whichever platform the item uses. A *handler* decides where, so a convention
picks the platforms it uses rather than the portal assuming one. Programme
guides such as [ConClár](conclar.md) link to these URLs.

| Handler | Destination of a session | Replays |
| ------- | ------------------------ | ------- |
| `zoomevents` | Zoom Events session id. The member gets a ticket for the event, created through the API the first time they join, and is sent to the session | Zoom Events session id |
| `stream` | Room id. The room's current URL is managed under *Manage stream URLs*, so it can change without touching sessions | A YouTube URL is embedded without cookies; any other URL is redirected to |
| `discord` | Not used. Goes to `/deep-link/chat` for the item's [Discord post](discord.md#programme-item-chat) | Not supported |

Sessions are managed under Manage programme, or pushed from a programme tool
through [the API](api.md).

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

### Adding a handler

The deep-link pages never mention a platform, so a new handler doesn't touch
them.

1. Implement `ProgHandler` in `site/lib/prog_handlers/`. `join()` is given the
   item (`kind` is `session` or `replay`, plus `item_id`, `title`,
   `destination` and the logged in `member`) and returns a
   `ProgHandlerResult`: `redirect`, `message`, `embed` or `login_required`.
2. Add it to `prog_handler_factories()` in `site/lib/prog_handlers.php`.
3. Add its name to `AVAILABLE_PROG_HANDLERS`.

## Zoom Events

The `zoomevents` handler registers members for a Zoom Events event and sends
them to its sessions.

1. In the Zoom App Marketplace, create a Server-to-Server OAuth app with
   access to Zoom Events.
2. Create the event, and a ticket type for portal members.
3. Set the `[zoom_events]` section of the secrets file:

   ```ini
   [zoom_events]
   client_id = ...
   client_secret = ...
   account_id = ...
   event_id = ...
   ticket_type_id = ...
   ; Optional
   ticket_email_domain = example.com
   ```

4. Give each session the Zoom Events session id as its destination.

Tickets are registered as `zoomlogin+<badge number>@<ticket_email_domain>` so
members' own addresses aren't shared with Zoom; leave `ticket_email_domain`
out to use the member's email. A member's ticket is created the first time
they join and reused after that.

## Streams and replays

For the `stream` handler, a session's destination is a room id. Each room's
current stream URL is set under Manage programme → Manage stream URLs, so a
room can move to a new stream without editing its sessions.

Replays are managed under Manage programme too. With `PROG_REPLAY_HANDLER`
set to `stream`, a YouTube replay is embedded in the portal using
`youtube-nocookie.com`, and any other URL is redirected to.

## Sign-ups

The Sign-ups page (`/sign-ups`) lists links to sign-up forms, such as Google
Forms, for items with limited space. Members with `manage-programme` add them
under Manage programme, one at a time or by pasting CSV. A bulk add with any
invalid row imports nothing.

Entries are grouped by their free-text category, then by session start time.
Each has an optional opening and closing time; before it opens the page shows
when it will, and after it closes the link is replaced with "Closed". The links
are only shown to members with the `see-signup-links` permission.

To fill in member details, put `{name}`, `{email}` or `{badge_no}` anywhere in
the sign-up URL, for example
`https://forms.example.com/f?entry.1={name}&entry.2={badge_no}`. For someone
not logged in, query parameters holding a placeholder are removed,
and placeholders elsewhere in the URL are left empty.
