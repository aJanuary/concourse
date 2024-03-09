# Members page for Levitation 2024

## Deployment
Log in to Hostinger. See the password spreadsheet for the credentials.
The db-migrations files can be run against the database manually by pasting into the SQL page of myPHPAdmin.
scp the contents of the `site` to `/home/u943682649/members` or `/home/u943682649/members-staging` on the server.
`site/includes/secrets.php` contains secrets that are not committed to git. See `site/includes/secret.php.example` for the format.