FROM php:7.4-cli-alpine
MAINTAINER Norbert Kowalik <norbert.kowalik@icloud.com>

# Installing Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installing xdebug

RUN set -ex \
    && apk update \
    && apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug  \
    && docker-php-ext-enable xdebug

COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini