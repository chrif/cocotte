#!/usr/bin/env sh

set -eu

export ACME_CASERVER='--acme.caserver=https://acme-staging.api.letsencrypt.org/directory'
export ACME_STORAGE=/opt/ssl/acme/acme-staging.json

sh bin/prod.sh
