FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor bash postgresql-dev icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql bcmath intl zip gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html
WORKDIR /var/www/html

COPY conf/nginx/nginx-site.conf /etc/nginx/http.d/default.conf
COPY conf/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY scripts/start.sh /start.sh

RUN chmod +x /start.sh \
    && composer install --no-dev --optimize-autoloader

EXPOSE 80

CMD ["/start.sh"]
