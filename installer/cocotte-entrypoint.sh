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

# for mounted files in dev
chmod +x /installer/bin/* /installer/php/bin/* /installer/php/vendor/bin/*

exec "$@"
