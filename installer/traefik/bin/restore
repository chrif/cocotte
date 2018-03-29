#!/usr/bin/env sh

set -eu

restore() {
	local file=${1:-}

	docker container create --name restore -v traefik_ssl:/opt/ssl alpine
	docker cp \
		var/acme/${file}.json \
		restore:/opt/ssl/acme/${file}.json
	docker run --rm -v traefik_ssl:/opt/ssl alpine chmod 600 /opt/ssl/acme/${file}.json
	docker rm restore
}

eval $(docker-machine env ${MACHINE_NAME})
docker volume create traefik_ssl
docker run --rm -v traefik_ssl:/opt/ssl alpine \
	mkdir -p /opt/ssl/acme && chmod 700 /opt/ssl/acme

restore "acme"
restore "acme-staging"
