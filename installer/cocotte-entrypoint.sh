#!/bin/sh

set -eu

if [ "$1" = 'create' ]; then exec sh create
elif [ "$1" = 'remove' ]; then exec sh remove
elif [ "$1" = 'console' ]; then shift; cd php; exec bin/console "$@";
elif [ "$1" = 'phpunit' ]; then shift; cd php; exec vendor/bin/phpunit "$@";
else exec "$@";
fi
