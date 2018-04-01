#!/bin/sh

set -eu

# This is crooked but it serves our purpose:
# Create a path on installer identical to the storage path on host.
# Because docker machine stores an absolute path in its config files and
# we want it to work outside of the installer afterwards.
# This solution is preferred to editing the json config files after machine creation.
if ! [ -e "$MACHINE_STORAGE_PATH" ]; then
	if [ -d "/host/machine" ] && ! [ -d "/host/machine/certs" ]; then
		>&2 echo "Error: Tried to create a directory named 'machine' on host but it already exists and it is not a valid docker machine storage path."
		exit 1
	fi
	mkdir -p $(dirname "$MACHINE_STORAGE_PATH")
	mkdir -p /host/machine/certs
	ln -sfn /host/machine "${MACHINE_STORAGE_PATH}"
elif ! [ -L "$MACHINE_STORAGE_PATH" ]; then
	>&2 echo "Error: cannot symlink MACHINE_STORAGE_PATH to '$MACHINE_STORAGE_PATH' because it is a real path on installer. Start installer from a different directory on your computer."
	exit 1
fi

# for mounted files in dev (does nothing in prod, they are already executable)
chmod +x /installer/bin/* /installer/php/bin/* /installer/php/vendor/bin/*

if [ "$1" = 'phpunit' ]; then shift; cd php; phpunit "$@"
else exec "$@"
fi
