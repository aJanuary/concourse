#!/usr/bin/env bash
#
# Pulls the images named in an env file and restarts the portal on them:
#
#   ./redeploy.sh                 # uses .env.prod
#   ./redeploy.sh .env.staging
#
# A relative env file is looked for next to this script.
#
set -eu -o pipefail

APP_DIR=$(unset CDPATH && cd "$(dirname "$0")" && pwd)
ENV_FILE="${1:-.env.prod}"

cd "${APP_DIR}"

if [[ "${ENV_FILE}" != /* ]]; then
	ENV_FILE="${APP_DIR}/${ENV_FILE}"
fi

if [[ ! -f "${ENV_FILE}" ]]; then
	echo "Environment file not found: ${ENV_FILE}" >&2
	exit 1
fi

docker compose --env-file "${ENV_FILE}" pull
docker compose --env-file "${ENV_FILE}" down
docker compose --env-file "${ENV_FILE}" up -d
