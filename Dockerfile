###############################################################################
# PHP BASE
###############################################################################
FROM php:8.2-apache-bookworm AS php-base

RUN apt-get update \
 && apt-get install --no-install-recommends -y \
        libicu-dev \
        libzip-dev \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) intl \
 && docker-php-ext-install -j$(nproc) opcache \
 && docker-php-ext-install -j$(nproc) pdo_mysql \
 && docker-php-ext-install -j$(nproc) zip \
 && rm -rf /tmp/*

###############################################################################
# PHP BUILDER
###############################################################################
FROM php-base AS php-builder

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update \
 && apt-get install --no-install-recommends -y \
        git \
        libfreetype6-dev \
        unzip \
        wget \
        zip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /build
COPY . /build/

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev

###############################################################################
# ASSETS BUILDER
###############################################################################
FROM node:18-alpine as assets-builder

RUN apk add \
    git \
    python3 \
    yarn

WORKDIR /build
COPY assets                                                     /build/assets
COPY patches                                                    /build/patches
COPY package.json postcss.config.js webpack.config.js yarn.lock /build/
# Needed for bundles providing assets (omines/datatables-bundle)
COPY --from=php-builder /build/vendor                           /build/vendor

RUN yarn install --pure-lockfile \
 && yarn encore production

###############################################################################
# FINAL IMAGE
###############################################################################
FROM php-base

# Apache+PHP configuration
COPY ./docker/prod/etc/php.ini    /usr/local/etc/php/php.ini
COPY ./docker/prod/etc/vhost.conf /etc/apache2/sites-enabled/000-default.conf

RUN ln -sf /dev/stdout /var/log/apache2/access.log \
 && ln -sf /dev/stderr /var/log/apache2/error.log \
 && touch /var/log/php.log \
 && chmod 666 /var/log/php.log

RUN a2enmod rewrite \
 && a2enmod deflate \
 && a2enmod expires

RUN sed -i 's/^ServerTokens OS/ServerTokens Prod/' /etc/apache2/conf-available/security.conf \
 && sed -i 's/^ServerSignature On/ServerSignature Off/' /etc/apache2/conf-available/security.conf \
 && a2enconf security

# Copy webapp files
COPY --from=php-builder    /build              /var/www/webapp
COPY --from=assets-builder /build/public/build /var/www/webapp/public/build

# Clear up content in var/
RUN rm -rf /var/www/webapp/var/*

# Create var/ directories
RUN mkdir -m 0777 -p \
    /var/www/webapp/var/cache \
    /var/www/webapp/var/db \
    /var/www/webapp/var/log

WORKDIR /var/www/webapp

EXPOSE 80
