FROM chrif/php:7.2-fpm-alpine

WORKDIR /opt/app

ARG APP_LOG_DIR
ENV APP_LOG_DIR ${APP_LOG_DIR}

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev
COPY . ./
RUN chmod +x /opt/app/bin/console && \
	mkdir -p /opt/log/docker && \
	mkdir -p /opt/log/nginx && \
	mkdir -p /opt/log/php && \
	mkdir -p /opt/log/symfony && \
	chown www-data:www-data /opt/log/symfony && \
	chmod g+s /opt/log/symfony && \
	# with APP_ENV=prod and APP_DEBUG=1, php-fpm needs write permissions on /opt/app/var/cache/prod
	mkdir -p /opt/app/var/cache/prod && \
	chown www-data:www-data /opt/app/var/cache/prod && \
	chmod g+s /opt/app/var/cache/prod && \
	APP_ENV=prod composer dump-autoload --optimize --no-dev --classmap-authoritative

USER www-data
RUN APP_ENV=prod bin/console cache:warmup

ENTRYPOINT /bin/true
