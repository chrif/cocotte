#!/bin/sh
docker stop traefik traefikdata
docker rm traefik traefikdata
docker-compose down -v --remove-orphans
docker volume rm traefikssl
