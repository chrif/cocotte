#!/bin/sh
sh docker/bin/backup.sh
sh docker/bin/rm.sh
sh docker/bin/init.sh
docker-compose -f docker-compose.yml up -d --build traefik
