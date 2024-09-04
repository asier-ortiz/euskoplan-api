# Usar Ubuntu como base
FROM ubuntu:20.04

# Evitar interacción en la instalación
ARG DEBIAN_FRONTEND=noninteractive

# Instalar Apache, dependencias necesarias y limpiar caché
RUN apt-get update && apt-get install -y \
    apache2 \
    libapache2-mod-fcgid \
    nano \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && a2enmod proxy_fcgi \
    && a2enmod rewrite

# Copiar los archivos de configuración de Apache
COPY docker-compose/apache/apache2.conf /etc/apache2/apache2.conf
COPY docker-compose/apache/api.euskoplan.conf /etc/apache2/sites-enabled/000-default.conf

# Iniciar Apache en primer plano
CMD ["apachectl", "-D", "FOREGROUND"]
