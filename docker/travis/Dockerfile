FROM ruby:alpine
RUN	apk add --no-cache build-base git && \
	gem install travis && \
	gem install travis-lint && \
	apk del build-base
WORKDIR /opt/app
ENTRYPOINT ["travis"]
