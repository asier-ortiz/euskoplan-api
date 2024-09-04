# Usar la imagen base de PHP-FPM
FROM php:8.1-fpm

# Instalar dependencias incluyendo cron
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    cron

# Limpiar caché de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring exif pcntl bcmath gd zip

# Instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar y configurar Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configurar archivos de configuración de Xdebug y errores de PHP
COPY /docker-compose/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY /docker-compose/php/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

# Instalar y habilitar Redis
RUN pecl install redis && docker-php-ext-enable redis

# Copiar archivo de cron de Laravel
COPY laravel_cron /etc/cron.d/laravel_cron

# Dar permisos al archivo cron y aplicar crontab
RUN chmod 0644 /etc/cron.d/laravel_cron
RUN crontab /etc/cron.d/laravel_cron

# Crear archivo de log para cron
RUN touch /var/log/cron.log

# Comando que inicia cron y PHP-FPM simultáneamente
CMD cron && php-fpm
