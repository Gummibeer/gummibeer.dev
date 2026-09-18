FROM node:24-bookworm-slim AS node

FROM php:8.5-cli-bookworm

ARG ACTIONLINT_VERSION=1.7.12

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/root/.composer
ENV COMPOSER_NO_INTERACTION=1

WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libxml2-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd intl simplexml \
    && rm -rf /var/lib/apt/lists/*

COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && npm install --global npm@11.17.0

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN composer global config allow-plugins.ergebnis/composer-normalize true \
    && composer global require ergebnis/composer-normalize:^2.52 --no-progress \
    && curl -fsSL "https://github.com/rhysd/actionlint/releases/download/v${ACTIONLINT_VERSION}/actionlint_${ACTIONLINT_VERSION}_linux_amd64.tar.gz" \
        | tar -xz -C /usr/local/bin actionlint

COPY composer.json composer.lock package.json package-lock.json ./

RUN composer install --prefer-dist --no-progress --no-scripts --no-autoloader \
    && npm ci --no-audit --no-fund

COPY . .

RUN composer dump-autoload --optimize --no-scripts \
    && cp .env.example .env \
    && php artisan package:discover --ansi --no-interaction
