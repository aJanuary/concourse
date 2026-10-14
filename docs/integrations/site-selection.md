# Site selection

At a Worldcon, members who pay the site selection fee, an advance membership
of the Worldcon being voted on, can vote for where it is held two years
later. The portal takes the fee online through
[Square](https://squareup.com/) and hands out voter keys for an external
election platform, such as [ElectionBuddy](https://electionbuddy.com/). At
the convention, staff at the site selection desk sell paper ballots.

## How it works

Online:

1. Staff load a pool of the election platform's single-use voter keys
   ("tokens") into the portal.
2. A member opens the Site selection page and chooses to pay. They give their
   postal address and country, and whether the winning bid may publish their
   name and should send them paper publications.
3. The portal sends them to a Square payment link for the fee. After paying,
   Square sends them back to `/site-selection/processing`.
4. Square tells the portal about the payment through its webhook, and the
   portal assigns the member a key from the pool. The processing page waits
   for this, then sends them to the Site selection page, which now has a
   "Vote" button linking to the election platform with their key.

If the webhook takes longer than about a minute, the processing page tells
the member their voting link will be on the Site selection page once the
payment is confirmed, and who to contact if it isn't.

On paper, at the convention, see [The desk](#the-desk).

## Who can see it

| Permission | Lets you |
|---|---|
| `see-site-selection` | See the Site selection card and page, and pay. The `site-selection` role has it |
| `sell-tokens` | Use the [site selection desk](#the-desk) |
| `manage-tokens` | [Manage the keys](#voter-keys), list spoilt keys, export members' details, and at the desk give a member a new key or unassign a paper ballot |

Give the `site-selection` role to the members who may vote, for example from
your identity provider at login:

```ini
[oidc]
role_map[site-selection-can-vote] = site-selection
```

or by hand under [Manage roles](../roles-and-permissions.md#managing-roles).
`see-site-selection-coming-soon` shows the card greyed out before it opens
(see [Coming soon](../cards.md#coming-soon)).

## Setting up Square

1. In the [Square Developer Console](https://developer.squareup.com/apps),
   create an application. Use its sandbox to try everything out first.
2. In Square, create a catalogue item for the site selection fee. Its price
   is what members are charged. Note the id of its variation.
3. Under the application's webhooks, subscribe to `payment.updated` at
   `https://<your portal>/site-selection/square-webhook`. Note the
   subscription's signature key.
4. Add a `[square]` section to the secrets file:

   ```ini
   [square]
   access_token = <the application's access token>
   environment = sandbox
   location_id = <the location to take payments at>
   catalog_item_variation_id = <the fee's variation id>
   webhook_signature_key = <the webhook's signature key>
   ;notification_url = https://portal.example.com/site-selection/square-webhook
   ```

   `environment` is `sandbox` (the default) or `production`.
   `access_token` and `webhook_signature_key` can be
   [encrypted](../configuration.md#encrypted-secrets).

Square signs each webhook with the URL it was sent to, so the portal must
know exactly the URL registered with Square. By default it is the portal's
own address followed by `/site-selection/square-webhook`. If the portal is
behind a proxy that changes the address, set `notification_url` to the URL
registered with Square.

Each payment link's order has the member's badge number as its reference, so
you can find a member's payment in Square's dashboard. A member gets one
payment link, made the first time they choose to pay. In the sandbox a new
link is made each time.

Without a tunnel from Square to a development portal, test the webhook with
`test/square-webhook/send_webhook.py`, which sends a signed
`payment.updated` event for a sandbox order. It signs with `[square]
webhook_signature_key` from `secrets/web_secrets.ini`, decrypting it if it is
encrypted, as [the API tester](api.md) does.

## Voter keys

Staff with `manage-tokens` manage the keys from the Manage site selection
card. Generate the keys on the election platform and add them under "Add
keys", one per line as `label,key`, for example `Batch A 001,abc-def-ghi`.
The label tells keys apart without showing the key. Nothing is added unless
every row is fine, and keys already in the pool are refused.

If the pool runs out, members who pay don't get a key, and the webhook fails
so that Square sends it again later. Adding keys before Square gives up
assigns them. Keep an eye on how many are available, shown at the top of the
list.

From the list you can also:

- unassign keys, which returns them to the pool for someone else, even if
  they were spoilt;
- delete keys that aren't assigned to anyone.

### Spoilt keys

A member's key is spoilt when they move to a paper ballot or are given a new
key, for example because theirs was leaked. Spoilt keys are listed under
"Spoilt keys", newest first. Void them on the election platform so they
can't be used.

### Exporting for the winning bid

"Export member details" downloads a CSV of everyone who paid online and can
vote online: their badge number and name, real name, email, country,
address and preferences. Pass it to the winning bid. Members voting on paper
aren't included, as their details are on their ballot. Cells that a
spreadsheet would treat as formulas are prefixed with `'`.

## The desk

Staff with `sell-tokens` use the Site selection desk card at the convention.
Search for a member by badge number, name or email. Then, depending on the
member:

| Member | Action |
|---|---|
| Hasn't paid | Take the fee, then "Sell paper ballot" and give them a paper ballot |
| Can vote online | "Move to paper ballot" spoils their key, so give them a paper ballot without taking the fee again. With `manage-tokens`, "Give new online key" spoils their key and assigns another |
| Voting on paper | With `manage-tokens`, "Unassign paper ballot" undoes a sale made by mistake |

Each action asks for confirmation, naming the member.

The desk finds members through the [member source](../logging-in.md#registration-systems).
The default only finds members who have logged in to the portal. If members
may buy a paper ballot without having logged in, implement `search()` against
your registration system.

## The page

The page's wording and links come from `SITE_SELECTION` in
`site/lib/config.php`:

| Key | Purpose |
|---|---|
| `year`, `ordinal` | The Worldcon being voted on, as in "the 85th Worldcon will be held in 2027" |
| `fee` | The fee as shown to members, such as `$50 USD`. Square's catalogue item sets what is charged |
| `bids` | The bids, each `['name' => ..., 'url' => ..., 'withdrawn' => false]`, shown in a random order. A withdrawn bid is struck through |
| `info_url` | The convention's page about how site selection works |
| `paper_ballot_url` | The paper ballot, offered to members not already voting on paper |
| `ballot_url` | The election platform's voting page, with `{token}` where the member's key goes, such as `https://secure.electionbuddy.com/{token}` |
