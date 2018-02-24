FROM chrif/php:7.2-fpm-alpine

WORKDIR /opt/app

ARG APP_LOG_DIR
ENV APP_LOG_DIR ${APP_LOG_DIR}

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev
COPY . ./
RUN APP_ENV=prod composer dump-autoload --optimize --no-dev --classmap-authoritative && \
	chmod +x /opt/app/bin/console && \
	APP_ENV=prod bin/console cache:warmup && \
	mkdir -p /opt/log/docker && \
	mkdir -p /opt/log/nginx && \
	mkdir -p /opt/log/php && \
	mkdir -p /opt/log/symfony && \
	chown www-data:www-data /opt/log/symfony && \
	chmod g+s /opt/log/symfony

ENTRYPOINT /bin/true
