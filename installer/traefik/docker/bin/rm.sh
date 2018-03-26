#!/bin/sh
docker-compose down -v --remove-orphans
docker volume rm traefikssl
