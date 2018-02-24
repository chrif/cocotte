#!/usr/bin/env sh
set -eu
docker-compose run --rm -v "$(pwd)":/opt/app/host php console app:machine:create
sh create-machine.sh
rm create-machine.sh
