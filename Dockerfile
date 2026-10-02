FROM php:8.4-cli-alpine

RUN apk add --no-cache $PHPIZE_DEPS git unzip \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
