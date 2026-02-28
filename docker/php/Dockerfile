FROM php:8.1-fpm

# Instalar extensões necessárias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar permissões
RUN useradd -G www-data,root -u 1000 -d /home/dev dev \
    && mkdir -p /home/dev/.composer \
    && chown -R dev:dev /home/dev

WORKDIR /var/www/html

USER dev