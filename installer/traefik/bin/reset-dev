#!/usr/bin/env sh

set -eux

eval $(docker-machine env -u)
docker stop traefik || true
docker rm traefik || true
docker-compose down -v --remove-orphans
docker volume rm traefikssl || true
docker network rm traefik || true