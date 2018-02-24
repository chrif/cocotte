FROM chrif/php:7.2-fpm-alpine

WORKDIR /opt/app

# export path
ENV ENV="/root/.ashrc"

COPY php.ini /usr/local/etc/php/php.ini
COPY php-fpm.conf /usr/local/etc/php-fpm.d/zzz-prod.conf

RUN ln -s /opt/app/bin/console /usr/local/bin/console && \
	rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
	echo 'PATH=/opt/app/bin:/opt/app/vendor/bin:$PATH' >> /root/.ashrc && \
	echo "alias c='console'" >> /root/.ashrc
