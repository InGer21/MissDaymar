FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor bash postgresql-dev \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html
WORKDIR /var/www/html

COPY conf/nginx/nginx-site.conf /etc/nginx/http.d/default.conf
COPY conf/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY scripts/start.sh /start.sh

RUN chmod +x /start.sh \
    && composer install --no-dev --optimize-autoloader \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 80

CMD ["/start.sh"]
