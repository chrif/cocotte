FROM mysql:5.7.19

ARG password="password="
ARG MYSQL_ROOT_PASSWORD

RUN echo '[client]' >> /etc/mysql/conf.d/autologin.cnf \
 && echo 'user=root' >> /etc/mysql/conf.d/autologin.cnf \
 && echo $password$MYSQL_ROOT_PASSWORD >> /etc/mysql/conf.d/autologin.cnf
