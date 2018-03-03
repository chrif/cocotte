FROM chrif/cocotte-base

WORKDIR /opt/app

ARG APP_LOG_DIR
ENV APP_LOG_DIR ${APP_LOG_DIR}

COPY docker/php/php.ini /etc/php7/php.ini
COPY docker/php/php-fpm.conf /etc/php7/php-fpm.d/zzz.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

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
USER root

ENV ENV="/root/.ashrc"
RUN ln -s /opt/app/bin/console /usr/local/bin/console && \
	echo 'PATH=/opt/app/bin:/opt/app/vendor/bin:$PATH' >> /root/.ashrc && \
	echo "alias c='console'" >> /root/.ashrc

CMD ["nginx", "-g", "daemon off;"]
