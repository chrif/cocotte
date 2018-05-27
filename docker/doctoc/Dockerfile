FROM node:7.4.0-alpine

RUN npm install -g doctoc && \
	mkdir /usr/src

WORKDIR /usr/src

ENTRYPOINT ["doctoc"]
