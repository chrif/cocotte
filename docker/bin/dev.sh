#!/bin/sh
set -eu
eval $(cat .env .env-override) docker-compose up --build app
