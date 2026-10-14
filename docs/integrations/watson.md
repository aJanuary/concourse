# Watson

[Watson](https://github.com/ajanuary/watson) is a Discord bot for
conventions. It runs separately from the portal, and is the companion bot
the [Discord](discord.md) integration talks to: it gives members roles in
the server and creates the programme item posts. Its README covers running
it and every setting; this page covers connecting it to the portal.

## The API key

Watson signs its requests to [the API](api.md) as the client `Watson`. Add
a secret for it to the portal's secrets file:

```ini
[api_keys]
Watson = <a long random string>
```

and the same string to Watson's secrets file:

```yaml
portalApiKey: <a long random string>
```

## Roles in the server

Watson's `membership` module checks each member who joins the server, and
its `api` module listens for the portal's messages, so both are needed for
[roles in the server](discord.md#roles-in-the-server):

```yaml
api:
  channel: api-messages
membership:
  membershipApiUrl: https://portal.example.com/api/check-discord-ids
  additionalRoles:
    Staff (committee): staff
```

`DISCORD_API_CHANNEL_ID` in `site/lib/config.php` must be the id of the
`api` module's channel, and the portal's bot needs permission to send
messages in it.

Members the portal knows get the `memberRole` role, plus the Discord role
`additionalRoles` maps each of their portal roles to. Accounts it doesn't
know get the `unverifiedRole` role and a private thread in the
`helpDeskChannel` with the portal's link to claim the account. Once the
member follows the link and confirms, the portal asks Watson to check them
again.

## Programme item posts

Watson's `programme` module creates a forum post for each item in a ConClár
programme file and, with `assignDiscordPostsApiUrl` set, sends the posts to
the portal for [programme item chat](discord.md#programme-item-chat):

```yaml
programme:
  programmeUrl: https://guide.example.com/programme.json
  assignDiscordPostsApiUrl: https://portal.example.com/api/assign-discord-posts
  locations:
    - id: room-a
      name: Room A
```

`locations` maps the programme file's location names to the portal's room
ids (see [Programme handlers](programme.md#programme-handlers)). Watson
sends each post when it creates it, overwriting the item's post under
Manage Discord posts.

Watson's other modules (reminders, "Now on" messages and the newsletter)
don't involve the portal.
