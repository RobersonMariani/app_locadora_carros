FROM php:8.5-fpm

ARG user=appuser
ARG uid=1000

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/$user $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

WORKDIR /var/www

RUN chown -R $user:$user /var/www

COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

COPY --chown=$user:$user composer.json composer.lock ./

USER $user

RUN composer install --no-interaction --optimize-autoloader --no-scripts

COPY --chown=$user:$user . .

RUN composer dump-autoload --optimize

EXPOSE 9000

CMD ["php-fpm"]
