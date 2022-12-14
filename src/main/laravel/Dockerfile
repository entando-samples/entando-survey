FROM composer:2.0 as build
COPY . /app/
RUN composer install --prefer-dist --optimize-autoloader --no-interaction

FROM php:7.4-apache-buster as production

# # ENV APP_ENV=production
# # ENV APP_DEBUG=false
# # Set working directory
WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Add user for laravel application
# RUN groupadd -g 1000 www
# RUN useradd -u 1000 -ms /bin/bash -g www www

# # Install PHP extensions
RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy existing application directory contents
COPY --from=build /app /var/www

# # Copy existing application directory permissions
COPY --from=build --chown=www-data:www-data /app /var/www
RUN chgrp -R 0 /var/www && chmod -R g=u /var/www

RUN sed -i "s/Listen 80/Listen 8081/" /etc/apache2/ports.conf
# # Change current user to www

USER www-data

# # Expose port 9000 and start php-fpm server
# EXPOSE 9000

#RUN chmod 777 -R /var/www/storage/ && \
#    chown -R www-data:www-data /var/www/ && \


# RUN php artisan config:cache && \
#   php artisan route:cache && \
RUN php artisan key:generate

USER root
EXPOSE 8081

RUN a2enmod rewrite

CMD [ "bash", "entrypoint.sh" ]
