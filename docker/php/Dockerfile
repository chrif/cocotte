FROM chrif/php

ARG APP_ENV=prod

COPY php.ini /usr/local/etc/php/php.ini
COPY php-fpm.conf /usr/local/etc/php-fpm.d/zzz-prod.conf

RUN ln -s /opt/app/bin/console /usr/local/bin/console && \
	if [ "$APP_ENV" = "dev" ]; \
		then echo "xdebug is on!"; \
		else echo "xdebug is off!" && rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
	fi

# export path
ENV ENV="/root/.ashrc"
RUN echo 'PATH=/opt/app/bin:/opt/app/vendor/bin:$PATH' >> /root/.ashrc && \
	echo "alias c='console'" >> /root/.ashrc

WORKDIR /opt/app
