FROM php:7.2-apache

RUN apt-get update
RUN apt-get install libpng-dev -y

RUN docker-php-ext-install zip mysqli pdo pdo_mysql gd
RUN a2enmod rewrite
RUN service apache2 restart

COPY ./ /var/www/html/
RUN chmod -R 757 /var/www/html/