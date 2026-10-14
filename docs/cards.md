# Cards

The home page is a set of cards in categories (Info, Programme, Participate
and Other). Each card links to a page in the portal or to another site, and
is only shown to members with its permission. Members can add any card they
can see to their quick links at the top of the page.

## Adding a card

### 1. Add it to the home page

Cards are listed in `$cards` in `site/web/index.php`, by category:

```php
'Programme' => [
  ...
  [
    'name' => 'art-auction',
    'title' => 'Art auction',
    'subtitle' => 'Bid online',
    'link' => '/art-auction',
    'card-permission' => 'see-art-auction',
  ],
],
```

| Key | Purpose |
|---|---|
| `name` | A unique id. It is the card's CSS class, the name recorded in [analytics](production.md#analytics) and the key for quick links, so don't change it once members have chosen their quick links |
| `title` | The card's text and link |
| `subtitle` | Shown under the title. Use `''` for none |
| `link` | A path in the portal, or a full URL. Links starting with `http` open in a new tab |
| `card-permission` | The permission needed to see the card. Required; a card without one is never shown |
| `live-card` | Optional. `true` when the image area shows live content rather than a picture (like the convention time card), so screen readers read it |

The link can depend on the member, for example to send members with a
permission straight to another site:

```php
'link' => current_user_has_permission('see-guide-redirect') ? 'https://guide.example.com/' : '/programme-guide',
```

A category is only shown when the member can see at least one of its cards.
To add a category, add a new key to `$cards`.

### 2. Add a permission

Permissions are rows in the `permissions` table, added with a migration (see
[Database changes](development.md#database-changes)):

```php
<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeArtAuctionPermission extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-art-auction']
            ])->saveData();
    }

    public function down(): void
    {
        $this->execute("DELETE FROM permissions WHERE name = 'see-art-auction'");
    }
}
```

Or reuse an existing permission if the card should be seen by the same
people as another.

Once deployed, grant the permission to roles under
[Manage roles](roles-and-permissions.md#managing-roles). Grant it to the
`default` role to show the card to everyone, including people who haven't
logged in.

### 3. Style it

The image and colours come from `site/web/resources/style.css`. Put an SVG
image in `site/web/resources/` and add rules for the card's `name`, copying
those of a card in the same category so it matches:

```css
.card.art-auction {
  background-color: var(--prog-color-light);
}
.card.art-auction .hero {
  background: var(--prog-bg);
}
.card.art-auction .hero::after {
  background-image: url('/resources/art-auction.svg');
}
```

The category colours are custom properties at the top of the file:
`--info-*`, `--prog-*`, `--part-*` and `--events-*`.

### 4. Quick links

In the script at the bottom of `site/web/index.php`:

- `favOrder` lists card names in the order they appear in quick links. Add
  the new card; cards not in the list go at the end.
- `$faved_cards` is the quick links a member starts with, which depend on
  their permissions. Add the card here to put it in quick links by default.
  Members who have already chosen keep their choices, but do get cards added
  to the defaults later.

### 5. Add the page

If the card links to a new page in the portal, add it to `site/web`. The URL
is the file name without `.php`, so `site/web/art-auction.php` is
`/art-auction`. Pages start like this:

```php
<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-art-auction');

render_header(
  'Art auction',
  'Bid in the ' . CON_NAME . ' art auction.',
  ['Home' => '/', 'Art auction']
);
?>
<article>
  <h2>Art auction</h2>
  ...
</article>
<?php
render_footer();
?>
```

`session_auth.php` sends people who aren't logged in to the login page. To
let them see the page with the `default` role's permissions instead, set
`$GLOBALS['app-data']['allow_implicit_anonymous'] = true;` before requiring
it (this only takes effect when `ALLOW_ANONYMOUS_ACCESS` is on).
`check_permission()` shows a 404 to members without the permission. Forms
that `POST` must include `<?php echo csrf_field(); ?>`, or they are rejected.

To link to a third party site with the member's details in it, see
[Linking to other sites](#linking-to-other-sites).

## Linking to other sites

A page can send the member to another site with their details in the URL, so
the other site doesn't need its own login. `site/lib/auth_functions.php` has
`get_current_user_badge_no()`, `get_current_user_name()` and
`get_current_user_email()`; `site/web/upload-photo.php` and
`site/web/virtual-gatherings.php` are examples. Anyone can edit a URL, so
only do this where it doesn't matter if the details are changed.

## Removing a card

Delete its entry from `$cards`, or take its permission away from every role
under Manage roles to hide it without a code change.
