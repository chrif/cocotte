FROM chrif/cocotte-base

# for detection to work in https://github.com/docker/machine/blob/3f20d202a2dad68f3c8d745f99f2439fb79454c9/commands/env.go#L223
ENV SHELL /bin/sh

# php
WORKDIR /installer/php
COPY ./php/composer.json ./
COPY ./php/composer.lock ./
# https://sentinelstand.com/article/composer-install-in-dockerfile-without-breaking-cache
RUN composer install --no-scripts --no-autoloader
COPY ./php ./
RUN composer install
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
WORKDIR /installer/bin
COPY ./bin ./
CMD ["install"]

# setup executables
RUN chmod +x /installer/bin/* /installer/php/bin/* /installer/php/vendor/bin/*
ENV PATH="/installer/bin:/installer/php/bin:/installer/php/vendor/bin:${PATH}"

ARG GIT_COMMIT_ID
ENV GIT_COMMIT_ID ${GIT_COMMIT_ID}

WORKDIR /installer
