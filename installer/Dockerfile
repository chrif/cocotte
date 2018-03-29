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
RUN ln -s /installer/php/bin/console /usr/local/bin/console
COPY php.ini /etc/php7/conf.d/zzz-php.ini

#traefik
WORKDIR /installer/traefik
COPY ./traefik ./

# entrypoint
WORKDIR /usr/local/bin/
COPY cocotte-entrypoint.sh ./
RUN chmod +x cocotte-entrypoint.sh
ENTRYPOINT ["cocotte-entrypoint.sh"]

# commands
WORKDIR /installer
COPY create remove machine-ip machine-is-running machine-exists ./
CMD ["sh", "create"]

# default env
ARG GIT_COMMIT_ID
ENV DIGITAL_OCEAN_API_TOKEN xxxxx
ENV GIT_COMMIT_ID ${GIT_COMMIT_ID}
ENV COCOTTE_MACHINE cocotte
ENV MACHINE_STORAGE_PATH /host/machine
ENV TRAEFIK_ACME_EMAIL me@example.com
ENV TRAEFIK_AUTH_BASIC \'username:\$password\'
ENV TRAEFIK_UI_HOST traefik.mydomain.com

# for detection to work in https://github.com/docker/machine/blob/3f20d202a2dad68f3c8d745f99f2439fb79454c9/commands/env.go#L223
ENV SHELL /bin/sh
