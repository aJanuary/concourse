# Logging in

`providers[]` in the `[auth]` section of the
[secrets file](configuration.md#the-secrets-file) lists the login providers,
in the order the login page shows them:

| Provider | Logs in by |
|---|---|
| `dev` | Choosing any existing member. Only works when `RUNTIME_ENV=development`. The default when no provider is listed |
| `local` | Email address, then a password or a code sent by email |
| `oidc` | An OpenID Connect identity provider, such as the registration system |

`allowlist[]` in `[auth]` limits logging in to the emails listed; empty means
everyone.

## Email login

The `local` provider lets a member enter their email address and then either
a password or a six digit code sent to that address. Enable it with
`providers[] = local` in the `[auth]` section, and set `[smtp]` and
`[crypto]`. For development, [mailpit](https://mailpit.axllent.org/) is a
convenient SMTP server that shows what was sent; the development environment
runs it.

Members are found by email through a `MemberSource`
(`site/lib/auth/MemberSource.php`). The default reads the `members` table. To
look members up in a registration system, see
[Registration systems](#registration-systems).

Several members can share an email address; they are offered a chooser after
logging in.

Code guesses, password guesses and emails sent are limited per email address
and per client address. Behind a proxy, set `client_ip_header` or every
visitor appears to share the proxy's address.

## OpenID Connect login

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
exist, and `DEFAULT_ROLES_BY_BADGE` still applies on top. See
[Roles given at login](roles-and-permissions.md#roles-given-at-login).

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

For a provider to develop against, see
[Developing against an OpenID Connect provider](development.md#developing-against-an-openid-connect-provider).

## Registration systems

The portal keeps its own `members` table, filled in as members log in.
There are two ways to connect it to a registration system:

- If the registration system is an OpenID Connect provider, use the
  [`oidc` login provider](#openid-connect-login). Member details come from its
  claims at each login, and `role_map` turns membership types into roles.
- For [email login](#email-login), implement `MemberSource`
  (`site/lib/auth/MemberSource.php`) to look members up by email in the
  registration system, and return it from `auth_member_source()` in
  `site/lib/auth/providers.php`. The default, `DbMemberSource`, reads the
  `members` table.

The [site selection desk](integrations/site-selection.md#the-desk) also finds
members through `MemberSource`, with its `search()` method. With the default,
it only finds members who have logged in. If members buy a paper ballot
before they have logged in to the portal, implement `search()` against the
registration system, whichever login provider you use.
