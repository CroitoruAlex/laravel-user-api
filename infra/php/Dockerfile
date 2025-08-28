FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    unzip git curl zip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install zip pdo_mysql mbstring bcmath gd

WORKDIR /var/www/laravel

RUN mkdir -p storage bootstrap/cache && chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
