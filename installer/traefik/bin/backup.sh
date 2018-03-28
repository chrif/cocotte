#!/bin/sh

set -eu

backup() {
	local file=${1:-}

	cp acme/${file}.json "acme/${file}.json.$(date +"%Y_%m_%d_%I_%M_%p")" || exit 1;

	docker cp traefik:/opt/ssl/acme/${file}.json acme/${file}.json || exit 1;
}

eval $(docker-machine env ${MACHINE_NAME})

# prod
backup "acme"

# staging
backup "acme-staging"
