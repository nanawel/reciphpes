FROM php:7-apache

RUN curl --location --output /usr/local/bin/composer https://github.com/composer/getcomposer.org/raw/master/web/download/1.10.6/composer.phar \
    && chmod +x /usr/local/bin/composer

RUN apt-get update && apt-get install --no-install-recommends -y \
        wget \
        zip \
        unzip \
        git \
        libfreetype6-dev \
        libicu-dev \
        libzip-dev \
        time \
        nano \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-install -j$(nproc) opcache \
    && docker-php-ext-install -j$(nproc) zip \
    && pecl install mongodb-1.7.4 \
    && docker-php-ext-enable mongodb \
    && rm -rf /tmp/*

RUN yes | pecl install xdebug-2.9.5 \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/*

ARG host_ip=172.17.0.1
COPY etc/xdebug.tmpl.ini /tmp/xdebug.ini
RUN cat /tmp/xdebug.ini >> /usr/local/etc/php/conf.d/xdebug.ini \
    && sed -i "s/{{HOST_IP}}/${host_ip}/" /usr/local/etc/php/conf.d/xdebug.ini \
    && rm -rf /tmp/*

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

ARG webapp_uid
ENV WEBAPP_UID=${webapp_uid}
RUN usermod --non-unique --uid ${webapp_uid} www-data

ENV COMPOSER_HOME=/tmp/webapp/.composer \
    COMPOSER_CACHE_DIR=/tmp/webapp/.composer/cache

WORKDIR /var/www/webapp

EXPOSE 80
