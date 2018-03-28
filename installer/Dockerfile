FROM chrif/cocotte-base

# php
WORKDIR /installer/php
COPY ./php/composer.json ./
COPY ./php/composer.lock ./
# https://sentinelstand.com/article/composer-install-in-dockerfile-without-breaking-cache
RUN composer install --no-scripts --no-autoloader
COPY ./php ./
RUN composer install
RUN chmod +x /installer/php/bin/console
COPY php.ini /etc/php7/conf.d/zzz-php.ini

# entrypoint
WORKDIR /usr/local/bin/
COPY cocotte-entrypoint.sh ./
RUN chmod +x cocotte-entrypoint.sh
ENTRYPOINT ["cocotte-entrypoint.sh"]

# commands
WORKDIR /installer/
COPY create remove machine-ip machine-is-running ./
CMD ["sh", "create"]

# default env
ARG GIT_COMMIT_ID
ENV DIGITAL_OCEAN_API_TOKEN xxxxx
ENV GIT_COMMIT_ID ${GIT_COMMIT_ID}
ENV MACHINE_NAME cocotte
ENV MACHINE_STORAGE_PATH /host/machine
ENV TRAEFIK_ACME_EMAIL me@example.com
ENV TRAEFIK_AUTH_BASIC \'username:\$password\'
ENV TRAEFIK_UI_HOST traefik.mydomain.com