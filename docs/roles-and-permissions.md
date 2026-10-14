# Roles and permissions

What a member sees is controlled by permissions, which are granted through
roles. Every card and most pages need a permission. Everyone, including
people who haven't logged in, has the permissions of the `default` role.

Permissions are created by migrations, and so are some roles (`default`,
`admin`, `online`, `in-person` and a few others). The `admin` role always has
every permission, including ones added later, and they cannot be taken away
from it. Almost nothing else is granted to start with: the `default` role has
no permissions, so the home page is empty until someone grants some.

## The first administrator

Nobody can manage roles until someone has the `admin` role or another role
with `manage-roles`, so the first administrator is set up in the database, for
example with `scripts/db-shell`.

Members are added to the `members` table when they first log in, so log in
once and skip this step if you can. A new database has no members, though,
and the `dev` provider and the default `local` member source only find
members already in the table, so with those add yourself first:

```sql
INSERT INTO members (badge_no, email, name)
  VALUES ('0001', 'you@example.com', 'Your Name');
```

Then give yourself the `admin` role, replacing `0001` with your badge number:

```sql
INSERT INTO member_roles (badge_no, role_id)
  SELECT '0001', role_id FROM roles WHERE name = 'admin';
```

Instead, the `admin` role can be given through
`DEFAULT_ROLES_BY_BADGE` or the OIDC `role_map` (see
[Roles given at login](#roles-given-at-login)). Everything else can then be
done under Manage roles.

## Managing roles

Members with `manage-roles` choose which permissions each role has under
Manage roles, and give or remove roles for one member at a time from "Edit a
member's roles" there. Members are found by badge number, name or email in
the `members` table, so a member usually has to have logged in once before
they can be given a role. Changes apply from the member's next page load.

A member with `manage-roles` can preview the portal as a role would see it by
adding `?role=<role name>` to a page's URL.

## Roles given at login

`DEFAULT_ROLES_BY_BADGE` in `site/lib/config.php` adds roles to members as
they log in with any provider. It maps role names to badge numbers, and
roles are only ever added:

```php
define('DEFAULT_ROLES_BY_BADGE', [
  'artist' => ['0042', '0043'],
]);
```

The `oidc` provider can also set roles from the provider's claims with
`role_map`; see [OpenID Connect login](logging-in.md#openid-connect-login).

Logging in can undo a change made by hand. A role in the OIDC `role_map` is
set from the provider's claims at every login, and a role listed for a badge in
`DEFAULT_ROLES_BY_BADGE` is given back at every login. The Manage roles page
marks these roles. You cannot remove a role from yourself if that would take
away your own `manage-roles`.
