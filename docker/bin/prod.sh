#!/bin/sh
set -eu
docker-compose -f docker-compose.yml up -d docker
docker-compose rm -f
