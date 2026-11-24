# PHP CLI image used by the Docker-based integration tests in tests/docker.
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-cli

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip sqlite3 \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions gd intl zip mysqli pdo_mysql pdo_sqlite opcache

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_CACHE_DIR=/composer-cache

WORKDIR /work
