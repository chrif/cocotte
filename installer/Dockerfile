FROM chrif/docker:18.03.0-ce-alpine3.7 AS docker
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
COPY php.ini /usr/local/etc/php/php.ini

WORKDIR /usr/local/bin/
COPY cocotte-entrypoint.sh ./
RUN chmod +x cocotte-entrypoint.sh
ENTRYPOINT ["cocotte-entrypoint.sh"]

ENV DIGITAL_OCEAN_API_TOKEN xxxxx
ENV MACHINE_NAME cocotte
ENV MACHINE_STORAGE_PATH /host/machine

WORKDIR /installer/
COPY create remove machine-ip machine-is-running ./
CMD ["sh", "create"]
