#!/usr/bin/env sh

set -eux

set -a
. .env
. .env-override
set +a

eval $(docker-machine env -u)

# take down
docker stop traefik || true
docker rm traefik || true
docker-compose down -v --remove-orphans
docker volume rm traefikssl || true

# build
docker volume create traefikssl
docker-compose -f docker-compose.yml build

# bring up
docker network create traefik || true
docker-compose up -d traefik
docker logs -f traefik
