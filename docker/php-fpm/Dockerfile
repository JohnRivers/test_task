FROM php:7.4-fpm-alpine

RUN addgroup -g 1001 docker && adduser www-data docker

RUN apk add --no-cache \
  libzip-dev \
  zip \
  tzdata

RUN docker-php-ext-install pdo_mysql zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- \
  --filename=composer \
  --install-dir=/usr/local/bin && \
  composer --version

# Set timezone
COPY ./timezone.ini /usr/local/etc/php/conf.d/timezone.ini
