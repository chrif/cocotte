#!/bin/sh
set -eu
docker-compose -f docker-compose.yml up -d app
docker-compose rm -f
