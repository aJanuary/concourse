# Business meeting

The WSFS Business Meeting can be held online on a platform that gives each
attendee their own join link, such as [Lumi](https://www.lumiglobal.com/).
The portal holds those links: staff upload them, and each member sees a
"Join" button with their own link on the Business Meeting page, along with
a link to a separate Business Meeting Discord server, the session dates and,
optionally, the Mark Protection Committee (MPC) election.

## Who can see it

The Business Meeting card and page need the `see-business-meeting`
permission, which the `business-meeting` role has. Give that role to the
members who may attend, for example from your identity provider at login:

```ini
[oidc]
role_map[bm-can-attend] = business-meeting
```

or by hand under [Manage roles](../roles-and-permissions.md#managing-roles).
Granting `see-business-meeting` to a wider role, such as `default`, also
works: the page only offers to join to members who have a link, and tells
the others why they might not and who to contact.

`see-business-meeting-coming-soon` shows the card greyed out before it opens
(see [Coming soon](../cards.md#coming-soon)).

## Join links

Staff with `manage-business-meeting` manage links from the Manage business
meeting card. Each link belongs to a badge number, which doesn't have to be a
member who has logged in yet. Join links let anyone who has them take part
as that member, so they are only shown on the list when you click "Show".

To load them in bulk, export each attendee's badge number and join link from
the meeting platform as a CSV file and upload it under "Upload links". The
first row is a header. The portal looks for columns named `badge_no` and
`join_link`, and uses the first two columns if the header doesn't name them,
so rename the export's columns or put them first. Uploading:

- adds links for new badge numbers and replaces the link for existing ones;
- uses the last row if a badge number appears more than once;
- skips, and lists, rows with a missing badge number or a link that isn't
  `https://`;
- leaves links for badge numbers not in the file alone, so you can upload
  just the new attendees.

Uploading links doesn't give anyone the `business-meeting` role.

## The page

The page's wording and dates come from `BUSINESS_MEETING` in
`site/lib/config.php`:

| Key | Purpose |
|---|---|
| `platform_name` | The meeting platform's name, as in "Join Lumi" |
| `platform_info_url` | Linked from the platform's name |
| `info_url` | The convention's own page about the business meeting |
| `guide_url` | Optional guidance for attendees, linked from the top of the page. `null` for none |
| `help_email` | Who members contact about access or problems joining |
| `no_link_note` | Shown to members without a link, after the usual reasons; for example how long before a session new members get access. `''` for none |
| `sessions` | When each session starts, as `'yyyy-mm-dd hh:mm'` in `TIMEZONE` |
| `duration` | Shown under the dates, for example `'Each session lasts about three hours.'`. `''` for none |
| `mpc_election` | `null`, or the MPC election's `url` and when it `opens` and `closes` (`'yyyy-mm-dd hh:mm'` in `TIMEZONE`) |

### The MPC election

When `mpc_election` is set, members with a join link are told when the
election opens, then get a "Vote" button linking to `url` until it closes,
then are told it has closed. Members without a link are told they don't have
access to it.

### Discord

The Business Meeting Discord server is separate from the convention's. Put
its invite code, the part after `https://discord.gg/`, in the secrets file:

```ini
[discord]
bm_invite = AbCdEf
```

Until it is set, members with a join link are told the server isn't open yet.
