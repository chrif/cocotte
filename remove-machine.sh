#!/bin/sh

set -eu

[ -f ".travis.yml" ] || { echo >&2 "Please cd into the bundle before running this script."; exit 1; }

docker-machine rm -y ${MACHINE_NAME};
