#!/bin/sh

set -eu

[ -f ".travis.yml" ] || { echo >&2 "Please cd into the bundle before running this script."; exit 1; }

docker-machine rm -y ${MACHINE_NAME};

docker-machine create \
	--driver digitalocean \
	--digitalocean-access-token ${DIGITAL_OCEAN_API_TOKEN} \
	--engine-opt log-driver="json-file" \
	--engine-opt log-opt="max-size=1m" \
	--engine-opt log-opt="max-file=10" \
	${MACHINE_NAME};

docker-machine rm -y ${MACHINE_NAME};
