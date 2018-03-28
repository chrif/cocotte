#!/bin/sh

sh bin/backup.sh
sh bin/rm.sh
sh bin/init.sh
docker-compose -f docker-compose.yml up -d --build traefik
