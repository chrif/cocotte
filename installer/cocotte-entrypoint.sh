#!/bin/sh

set -eu

if [ "$MACHINE_STORAGE_PATH" != "/host/machine" ]; then

	if [ -e "$MACHINE_STORAGE_PATH" ] && ! [ -L "$MACHINE_STORAGE_PATH" ]; then
		echo  "Error: cannot symlink MACHINE_STORAGE_PATH to '$MACHINE_STORAGE_PATH' because it is a real path on installer. Start installer from a different directory on your computer."
		exit 1
	fi

	mkdir -p $(dirname "$MACHINE_STORAGE_PATH")
	mkdir -p /host/machine
	ln -sfn /host/machine "${MACHINE_STORAGE_PATH}"

fi

if [ "$1" = 'create' ]; then exec sh create
elif [ "$1" = 'remove' ]; then exec sh remove
elif [ "$1" = 'console' ]; then shift; cd php; exec bin/console "$@";
elif [ "$1" = 'phpunit' ]; then shift; cd php; exec vendor/bin/phpunit "$@";
else exec "$@";
fi
