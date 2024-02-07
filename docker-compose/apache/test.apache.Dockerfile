FROM ubuntu:20.04

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    libapache2-mod-fcgid \
    cron \
    nano

RUN a2enmod proxy_fcgi

RUN a2enmod rewrite

COPY /docker-compose/apache/apache2.conf /etc/apache2/

COPY /docker-compose/apache/api.euskoplan.conf /etc/apache2/sites-enabled/

CMD ["apachectl", "-D", "FOREGROUND"]
