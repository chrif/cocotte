#!/bin/sh

export ACME_CASERVER='--acme.caserver=https://acme-staging.api.letsencrypt.org/directory'
export ACME_STORAGE=/opt/ssl/acme/acme-staging.json

sh bin/backup.sh
sh bin/rm.sh
sh bin/init.sh
docker-compose -f docker-compose.yml up -d --build traefik
