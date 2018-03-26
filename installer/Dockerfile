# docker run --rm  chrif/docker:17.12.0-ce cat /etc/alpine-release -> 3.7.0
FROM chrif/docker:17.12.0-ce AS docker
RUN apk del php7
RUN curl -L https://github.com/docker/machine/releases/download/v0.13.0/docker-machine-`uname -s`-`uname -m` >/tmp/docker-machine && \
    install /tmp/docker-machine /usr/local/bin/docker-machine
RUN rm -rf /tmp/* /var/cache/apk/*

# docker run --rm chrif/php:7.2.3-fpm-alpine3.7 cat /etc/alpine-release -> 3.7.0
FROM chrif/php:7.2.3-fpm-alpine3.7
COPY --from=docker / /
WORKDIR /installer/php
COPY ./php/composer.json ./
COPY ./php/composer.lock ./
# https://sentinelstand.com/article/composer-install-in-dockerfile-without-breaking-cache
RUN composer install --no-scripts --no-autoloader
COPY ./php ./
RUN composer install
RUN chmod +x /installer/php/bin/console

WORKDIR /usr/local/bin/
COPY cocotte-entrypoint.sh ./
RUN chmod +x cocotte-entrypoint.sh
ENTRYPOINT ["cocotte-entrypoint.sh"]

ENV DIGITAL_OCEAN_API_TOKEN xxxxx
ENV MACHINE_NAME cocotte
ENV MACHINE_STORAGE_PATH /host/machine

WORKDIR /installer/
COPY create remove machine-ip ./
CMD ["sh", "create"]
