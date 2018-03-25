#!/usr/bin/env sh

set -e

export MACHINE_IP=$(docker-machine -s ${MACHINE_STORAGE_PATH} inspect --format='{{.Driver.IPAddress}}' ${MACHINE_NAME})

if [ "$1" = 'create' ]; then
    exec sh create
elif [ "$1" = 'remove' ]; then
    exec sh remove
elif [ "$1" = 'console' ]; then
    shift
    exec php/bin/console "$@"
elif [ "$1" = 'phpunit' ]; then
    shift
    cd php
    exec php vendor/bin/phpunit "$@"
else
    exec "$@"
fi
