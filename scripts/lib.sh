# Shared by the dev helper scripts. Source it; it moves to the project root so
# docker compose finds the project and its .env.

ROOT=$(unset CDPATH && cd "$(dirname "$0")/.." && pwd)
cd "$ROOT"

# Prints the localhost URL of a service's published port
service_url() {
	local address
	if ! address=$(docker compose port "$1" "$2" 2>/dev/null) || [ -z "$address" ]; then
		echo "$1 is not running; start it with scripts/bootstrap or docker compose up -d" >&2
		exit 1
	fi
	echo "http://localhost:${address##*:}"
}

open_url() {
	echo "$1"
	if command -v open >/dev/null; then
		open "$1"
	elif command -v xdg-open >/dev/null; then
		xdg-open "$1"
	fi
}

# The db service runs Postgres if it has psql
db_is_postgres() {
	docker compose exec -T db sh -c 'command -v psql' </dev/null >/dev/null 2>&1
}
