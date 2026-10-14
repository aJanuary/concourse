# Discord

The portal lets members join the convention's Discord server with their
badge name as their nickname, and links Discord accounts to members so the
server can give them roles. It has three parts in Discord: an OAuth
application, a bot in the server, and optionally a companion bot that asks
the portal about members, such as [Watson](watson.md).

## Joining the server

1. In the [Discord Developer Portal](https://discord.com/developers/applications),
   create an application.
2. Under OAuth2, add the redirect `https://portal.example.com/chat_callback`.
3. Under Bot, create the bot and invite it to the server with permission to
   create invites and manage nicknames.
4. Set the secrets file:

   ```ini
   [discord]
   bot_token = ...
   client_id = ...
   client_secret = ...
   ```

5. In `site/lib/config.php` set `DISCORD_GUILD_ID` to the server's id and
   `DISCORD_INVITE_CHANNEL_ID` to the channel new members land in. (Turn on
   developer mode in Discord to copy ids.)

The Chat online card (`/chat`, permission `see-discord`) then has a button
that asks the member to authorise the application. The portal records their
Discord id and username, adds them to the server with their badge name as
nickname, and sends them to a single use invite in case adding them failed.

## Roles in the server

A companion bot such as [Watson](watson.md) can give members Discord roles that match their portal
roles. It calls `/api/check-discord-ids` through [the API](api.md) with
the server's members:

```json
{"discordUsers": [{"id": "123", "username": "someone"}]}
```

The response maps each id to the member's name and portal role names, or
for an unknown account, to a link where the member can log in and claim it.
`DISCORD_SERVICE_ACCOUNTS` in `config.php` lists accounts that aren't
members, such as shared desk accounts, with the name and roles to give them.

When a member links an account, the portal posts
`{"action": "recheck-user", "user-id": "..."}` to the channel
`DISCORD_API_CHANNEL_ID`, for the companion bot to update that member
straight away.

Members with `manage-discord-ids` can see and change which Discord accounts
belong to which members under Discord ids.

## Programme item chat

Each programme item can have a Discord post (for example a forum thread) for
discussing it. `/deep-link/chat?item_id=...` sends the member to the item's
post, walking them through logging in and joining the server first. Posts
are managed under Manage programme → Manage Discord posts, or pushed to
`/api/assign-discord-posts`, which [Watson](watson.md) does as it creates
them:

```json
[{"itemId": "1", "title": "Panel", "start": "2026-08-08 14:00", "mins": 60, "roomId": "room-a", "postUrl": "https://discord.com/channels/..."}]
```

Until members have the `see-discord-posts` permission, the link shows a
"not yet open" page.
