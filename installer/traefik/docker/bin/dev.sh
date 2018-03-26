#!/bin/sh
sh docker/bin/rm.sh
sh docker/bin/init.sh
eval $(cat .env .env-override) docker-compose up -d --build traefik
