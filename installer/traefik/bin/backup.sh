#!/usr/bin/env sh

set -eu

backup() {
	local file=${1:-}

	touch var/acme/${file}.json

	cp var/acme/${file}.json "var/acme/${file}.json.$(date +"%Y_%m_%d_%I_%M_%p")" || exit 1

	docker container create --name backup -v traefik_ssl:/opt/ssl alpine

	docker cp \
		backup:/opt/ssl/acme/${file}.json \
		var/acme/${file}.json || exit 1

	docker rm backup;

}

eval $(docker-machine env ${MACHINE_NAME})
docker volume create traefik_ssl
backup "acme"
backup "acme-staging"

