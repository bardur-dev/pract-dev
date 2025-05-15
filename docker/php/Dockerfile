FROM php:8.2-fpm


RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libcurl4-openssl-dev


RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd curl zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


