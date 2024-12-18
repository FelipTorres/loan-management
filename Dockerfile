# FROM php:8.3-alpine

# RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html/

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin --filename=composer

# COPY . .

RUN composer update
