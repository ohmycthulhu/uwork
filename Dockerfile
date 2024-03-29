FROM php:7.3-apache

RUN apt-get update && apt-get upgrade -y

# 1. Development packages
RUN apt-get install -y \
    git \
    zip \
    curl \
    sudo \
    unzip \
    supervisor \
    libzip-dev \
    libicu-dev \
    libbz2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libghc-gd-dev \
    libmcrypt-dev \
    libreadline-dev \
    g++

# 2. Apache configs + document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers

# 4. Start with base php config, then add extensions
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-configure gd --with-jpeg-dir=/usr \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install \
    bz2 \
    exif \
    iconv \
    intl \
    bcmath \
    opcache \
    calendar \
    mbstring \
    pdo_mysql \
    zip

# Enable Redis
RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

# 5. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. We need a user with the same UID/GID with host user
# so when we execute CLI commands, all the host file's ownership remains intact
# otherwise command from inside container will create root-owned files and directories

ARG uid=1000
RUN useradd -G www-data,root -u $uid -d /home/devuser devuser
RUN mkdir -p /home/devuser/.composer && \
    chown -R devuser:devuser /home/devuser

COPY . /var/www/html
RUN cd /var/www/html && composer install
RUN test -f .env || cp .env.example .env
# RUN rm -f public/storage

RUN php artisan key:generate && \
    php artisan jwt:secret -f

RUN php artisan storage:link
RUN php artisan route:cache

RUN chmod a+rw storage -R
RUN php artisan config:clear
