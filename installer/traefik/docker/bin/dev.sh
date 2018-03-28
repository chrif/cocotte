#!/bin/sh

set -a
. .env
. .env-override
set +a

sh docker/bin/rm.sh
sh docker/bin/init.sh
docker-compose up -d --build traefik
