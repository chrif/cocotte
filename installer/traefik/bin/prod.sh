#!/usr/bin/env sh

set -eu

set -a
. .env
set +a

eval $(docker-machine env ${MACHINE_NAME})

# backup
sh bin/backup.sh

# take down
docker stop traefik || true
docker rm traefik || true
docker-compose down -v --remove-orphans
docker volume rm traefikssl || true

# build
docker network create traefik  || true
docker volume create traefikssl  || true
docker-compose -f docker-compose.yml build

# restore backup
sh bin/restore.sh

# bring up
docker-compose -f docker-compose.yml up -d traefik
