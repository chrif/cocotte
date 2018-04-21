FROM nginx:1.12-alpine

COPY etc/nginx.conf /etc/nginx/conf.d/default.conf
COPY ./web /opt/app/web

WORKDIR /opt/app
