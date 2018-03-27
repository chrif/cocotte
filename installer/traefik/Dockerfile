FROM alpine:3.7

ADD traefik.prod.toml /opt/app/traefik.prod.toml
ADD acme/acme.json /opt/ssl/acme/acme.json
ADD acme/acme-staging.json /opt/ssl/acme/acme-staging.json

RUN chmod 700 /opt/ssl/acme && \
	chmod 600 /opt/ssl/acme/acme.json && \
	chmod 600 /opt/ssl/acme/acme-staging.json

WORKDIR /opt