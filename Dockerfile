FROM php:8.0-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    librabbitmq-dev

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets
RUN pecl install amqp && docker-php-ext-enable amqp

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html