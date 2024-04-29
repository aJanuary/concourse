# Members page for Glasgow 2024

## Deployment
Requirements:
* [https://docs.docker.com/compose/](docker-compose)

1. Clone the repository
2. Create a folder called `secrets` and populate it with the secrets.
   See the `secrets` section of the `docker-compose.yml` file for an explanation
   of what secrets should be placed in what file.
3. Run `docker-compose up` to start the server.
4. Go to [http://localhost:8080] for the portal, and [http://localhost:8081] for
   phpMyAdmin.

## Development

### Database changes
1. Create a new file in the `db-migrations` folder with the name
   `U<nnn>-<description>.sql` where `nnn` is the next number in the sequence and
   `description` is a short description of the change.
2. Put the SQL commands to apply the change in the file.
3. Run
   `docker-compose exec db bash -c 'mysql --verbose -u root -p$(cat /run/secrets/db_root_password) members < /root/db-migrations/U<nnn>-<description>.sql'`
   to apply the changes to the database.