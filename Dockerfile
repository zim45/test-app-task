FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .

RUN composer config --global allow-plugins.yiisoft/yii2-composer true
RUN composer install --no-dev --optimize-autoloader

ENV APACHE_DOCUMENT_ROOT /var/www/html/web
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!Listen 80!Listen 80!g' /etc/apache2/ports.conf

RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf