#!/bin/sh

set -a
. .env
. .env-override
set +a

sh bin/rm.sh
sh bin/init.sh
docker-compose up -d --build traefik
