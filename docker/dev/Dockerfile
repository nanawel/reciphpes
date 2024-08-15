FROM php:8.2-apache-bookworm

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Base packages
RUN apt-get update \
 && apt-get install --no-install-recommends -y \
        curl \
        git \
        gnupg \
        libfreetype6-dev \
        libicu-dev \
        libzip-dev \
        nano \
        time \
        unzip \
        wget \
        zip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
 && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
 && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
 && apt-get update \
 && apt-get install --no-install-recommends -y \
    nodejs \
    yarn \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) intl \
 && docker-php-ext-install -j$(nproc) opcache \
 && docker-php-ext-install -j$(nproc) pdo_mysql \
 && docker-php-ext-install -j$(nproc) zip \
 && rm -rf /tmp/*

# XDebug
RUN yes | pecl install xdebug-3.2.2 \
 && docker-php-ext-enable xdebug \
 && rm -rf /tmp/*

ARG host_ip=172.17.0.1
COPY etc/xdebug.tmpl.ini /tmp/xdebug.ini
RUN cat /tmp/xdebug.ini >> /usr/local/etc/php/conf.d/xdebug.ini \
 && sed -i "s/{{HOST_IP}}/${host_ip}/" /usr/local/etc/php/conf.d/xdebug.ini \
 && rm -rf /tmp/*

# Apache configuration
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

# Dev user
ARG webapp_uid
ENV WEBAPP_UID=${webapp_uid}
RUN usermod --non-unique --uid ${webapp_uid} www-data

ENV COMPOSER_HOME=/tmp/webapp/composer \
    COMPOSER_CACHE_DIR=/tmp/webapp/composer/cache \
    YARN_CACHE_FOLDER=/tmp/webapp/yarn/cache

WORKDIR /var/www/webapp

EXPOSE 80
