#!/usr/bin/env bash
docker network create traefik
set -eu
docker volume create cocottelog
