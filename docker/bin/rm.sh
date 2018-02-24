#!/bin/sh
set -eu
docker-compose down -v --rmi=all --remove-orphans
docker-compose rm -f
