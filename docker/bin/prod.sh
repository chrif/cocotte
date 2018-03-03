#!/bin/sh
set -eu
docker-compose -f docker-compose.yml up --build app
#docker-compose rm -f
