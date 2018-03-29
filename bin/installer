#!/usr/bin/env sh

set -eu

if [ "$1" = 'test' ]; then
    set -a
    . ../.env
    set +a
    shift
fi

docker run --rm \
    -e DIGITAL_OCEAN_API_TOKEN=${DIGITAL_OCEAN_API_TOKEN} \
    -e MACHINE_NAME=${MACHINE_NAME} \
    -e TRAEFIK_ACME_EMAIL=${TRAEFIK_ACME_EMAIL} \
    -e TRAEFIK_AUTH_BASIC=${TRAEFIK_AUTH_BASIC} \
    -e TRAEFIK_UI_HOST=${TRAEFIK_UI_HOST} \
    -v "$(pwd)":/host \
    chrif/cocotte $@
