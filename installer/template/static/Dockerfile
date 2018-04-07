FROM nginx:1.12-alpine

COPY etc/default.conf /etc/nginx/conf.d/default.conf
COPY ./web /opt/app/web

WORKDIR /opt/app
