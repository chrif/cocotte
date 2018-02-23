FROM chrif/php

WORKDIR /opt/app

COPY composer.json ./
COPY composer.lock ./

# https://sentinelstand.com/article/composer-install-in-dockerfile-without-breaking-cache
RUN composer install --no-scripts --no-autoloader --no-dev
COPY . ./
RUN composer install --no-dev

RUN chmod +x /opt/app/bin/console && \
	mkdir -p /opt/log/docker && mkdir -p /opt/log/nginx && mkdir -p /opt/log/php

ENTRYPOINT /bin/true
