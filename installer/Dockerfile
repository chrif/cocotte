FROM chrif/cocotte-base

# for detection to work in https://github.com/docker/machine/blob/3f20d202a2dad68f3c8d745f99f2439fb79454c9/commands/env.go#L223
ENV SHELL /bin/sh

RUN mkdir /host

WORKDIR /installer
COPY ./composer.json ./
COPY ./composer.lock ./
# https://sentinelstand.com/article/composer-install-in-dockerfile-without-breaking-cache
RUN composer install --no-scripts --no-autoloader
COPY ./bin ./bin
COPY ./config ./config
COPY docs ./docs
COPY ./phpunit.xml.dist ./
COPY ./src ./src
COPY ./template ./template
COPY ./test ./test
COPY ./var ./var
COPY ./etc/php.ini /etc/php7/conf.d/zzz-php.ini
RUN composer install
RUN composer clearcache

# setup executables
RUN chmod +x /installer/bin/* /installer/vendor/bin/*
ENV PATH="/installer/bin:/installer/vendor/bin:${PATH}"

ARG GIT_COMMIT_ID=""
ENV GIT_COMMIT_ID ${GIT_COMMIT_ID}

ARG COCOTTE_VERSION=""
ENV COCOTTE_VERSION ${COCOTTE_VERSION}

ENV MACHINE_NAME=cocotte

ENTRYPOINT ["cocotte-entrypoint"]
CMD ["help"]
