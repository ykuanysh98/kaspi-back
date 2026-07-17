FROM php:8.1-fpm-bookworm

WORKDIR /var/www

# phpredis кеңейтілімін орнату үшін оның кодын жүктейміз
RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.7.tar.gz | tar xz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts

# Жүйелік кітапханалар мен PHP кеңейтілімдерін орнату
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    procps \
    tzdata \
    ca-certificates \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libc-client-dev \
    libkrb5-dev \
    zip \
    nodejs \
    npm \
    unzip \
    libmagickwand-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install intl pdo_mysql mbstring pcntl bcmath soap zip redis \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap \
    && rm -rf /var/lib/apt/lists/*

# PECL арқылы қосымша кеңейтілімдерді орнату
RUN pecl install imagick timezonedb \
    && docker-php-ext-enable imagick timezonedb

# Composer орнату
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Жүйелік пайдаланушы құқығын реттеу
RUN usermod -u 1000 www-data

# Бастапқы кодты көшіреміз
COPY . /var/www

EXPOSE 9000
USER www-data

CMD ["php-fpm"]
