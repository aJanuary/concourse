# The API

Other systems call the portal's API with signed requests. Each client has a
name and a shared secret, listed in the `[api_keys]` section of the secrets
file:

```ini
[api_keys]
programme-tool = <a long random string>
```

A request has two headers:

```
X-Members-RequestTime: 2026-08-08T14:00:00Z
Authorization: members:1 <client name> <signature>
```

The signature is the hex HMAC-SHA256, keyed with the client's secret, of the
method, the path with its query string, the request time and the
base64 encoded body, joined by newlines. The request time must be within
five minutes of the server's clock. `test/api-tester/send_api_request.py`
signs and sends a request:

```
test/api-tester/send_api_request.py upsert-sessions '[{"itemId": "1", "title": "Panel", "destination": "room-a", "handler": "stream"}]' --client <client name>
```

It reads the client's secret from `secrets/web_secrets.ini`. If the secret is
[encrypted](../configuration.md#encrypted-secrets), it finds the key the way
the portal does, or from `secrets/config_encryption_key.txt`; set
`CONFIG_ENCRYPTION_KEY` if the key is somewhere else.

| Endpoint | Body | Does |
|---|---|---|
| `/api/upsert-sessions` | A list of `{itemId, title, destination, handler}` | Adds or updates programme sessions; see [Programme handlers](programme.md#programme-handlers) |
| `/api/delete-sessions` | A list of item ids | Deletes programme sessions |
| `/api/assign-discord-posts` | A list of `{itemId, title, start, mins, roomId, postUrl}` | Sets programme items' Discord posts; see [Programme item chat](discord.md#programme-item-chat) |
| `/api/check-discord-ids` | `{discordUsers: [{id, username}]}` | Looks up Discord accounts; see [Roles in the server](discord.md#roles-in-the-server) |
