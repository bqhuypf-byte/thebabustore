FROM php:8.3-fpm-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends libicu-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j2 pdo_mysql intl zip gd bcmath exif opcache \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY deploy/php.ini /usr/local/etc/php/conf.d/babu.ini
RUN printf '[www]\npm = dynamic\npm.max_children = 4\npm.start_servers = 2\npm.min_spare_servers = 1\npm.max_spare_servers = 2\n' > /usr/local/etc/php-fpm.d/zz-babu.conf

