#!/bin/sh

export ACME_CASERVER='--acme.caserver=https://acme-staging.api.letsencrypt.org/directory'
export ACME_STORAGE=/opt/ssl/acme/acme-staging.json

sh docker/bin/backup.sh
sh docker/bin/rm.sh
sh docker/bin/init.sh
docker-compose -f docker-compose.yml up -d --build traefik
