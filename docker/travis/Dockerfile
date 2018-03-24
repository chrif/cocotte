FROM ruby:alpine
RUN apk add --no-cache build-base git && \
    gem install travis && \
    gem install travis-lint && \
    apk del build-base &&\
    mkdir project

ENV TRAVIS_TOKEN xxx

ENV TRAVIS_CONFIG_PATH /opt/travis
VOLUME /opt/travis

WORKDIR /opt/project
VOLUME /opt/project

ENTRYPOINT ["travis"]
