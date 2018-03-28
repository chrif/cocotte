#!/bin/sh
touch acme/acme.json acme/acme-staging.json
docker network create traefik
docker volume create traefikssl
