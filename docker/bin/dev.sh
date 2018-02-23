#!/bin/sh

set -eu

[ -d "docker/bin" ] || { echo >&2 "Please cd into the bundle before running this script."; exit 1; }
readonly PROGDIR=$PWD/docker/bin

. ${PROGDIR}/functions.sh

main() {
	init_cocotte

	eval $(cat .env .env-override) docker-compose up -d --build docker
}
main $@
