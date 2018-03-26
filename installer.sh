#!/usr/bin/env sh

set -eu

docker run --rm \
    -e DIGITAL_OCEAN_API_TOKEN=${DIGITAL_OCEAN_API_TOKEN} \
    -e MACHINE_NAME=${MACHINE_NAME} \
    -v "$(pwd)":/host \
    chrif/cocotte $@
