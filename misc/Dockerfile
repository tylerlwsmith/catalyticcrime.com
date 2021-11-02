########
# Base #
########
FROM php:8.0.10-fpm as base

RUN mkdir /srv/app
WORKDIR /srv/app

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY --from=composer:2.1.6 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y nano vim

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev  python2 \
    libjpeg-dev libpq-dev  \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ \
    && docker-php-ext-install pdo pdo_pgsql opcache gd

RUN pecl install redis-5.3.4 \
    && pecl install xdebug-3.0.4 \
    && docker-php-ext-enable redis xdebug

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"
COPY misc/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# https://stackoverflow.com/a/57866652/7759523
RUN echo "max_file_uploads=100" >> /usr/local/etc/php/conf.d/docker-php-ext-max_file_uploads.ini
RUN echo "post_max_size=120M" >> /usr/local/etc/php/conf.d/docker-php-ext-post_max_size.ini
RUN echo "upload_max_filesize=120M" >> /usr/local/etc/php/conf.d/docker-php-ext-upload_max_filesize.ini

COPY webapp/ .

RUN chown -R www-data:www-data /srv/app/storage

RUN chown -R www-data:www-data /srv/app/bootstrap/cache

###############
# Development #
###############
FROM base as development
ENV COMPOSER_VENDOR_DIR="/srv/vendor"
RUN composer install
COPY misc/xdebug.ini "$PHP_INI_DIR/conf.d/xdebug.ini"

##############
# Production #
##############
FROM base AS production
ENV COMPOSER_VENDOR_DIR="/srv/app/vendor"
RUN composer install

