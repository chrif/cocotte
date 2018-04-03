FROM traefik:v1.5.4-alpine

RUN mkdir -p 	/opt/ssl/acme/ && \
	touch 		/opt/ssl/acme/acme.json && \
	touch 		/opt/ssl/acme/acme-staging.json && \
	chmod 600 	/opt/ssl/acme/acme.json &&\
	chmod 600 	/opt/ssl/acme/acme-staging.json && \
	chmod 700 	/opt/ssl/acme

ADD traefik.prod.toml /opt/app/traefik.prod.toml

WORKDIR /opt