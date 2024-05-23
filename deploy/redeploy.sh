#!/usr/bin/bash

cd /opt/portal/app

/usr/bin/docker compose pull && /usr/bin/docker compose down && /usr/bin/docker compose up -d
