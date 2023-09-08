FROM php:8.2-apache

RUN apt update && apt install -y nodejs npm libpng-dev zlib1g-dev libxml2-dev libzip-dev libonig-dev zip curl unzip && docker-php-ext-configure gd \
    && pecl install redis \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-ext-enable redis \
    && docker-php-source delete

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
    pcntl

RUN apt-get install -y \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libpng-dev libxpm-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd  --with-jpeg  --with-freetype
RUN docker-php-ext-install gd

RUN groupadd -r app -g 1000 && useradd -u 1000 -r -g app -m -d /app -s /sbin/nologin -c "App user" app && \
    chmod 755 /var/www/

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    echo "alias composer='COMPOSER_MEMORY_LIMIT=-1 composer'" >> /root/.bashrc


RUN docker-php-ext-install pdo pdo_mysql sockets

WORKDIR /var/www/

USER root

COPY --chown=www-data:www-data . /var/www/

RUN cp -rf /var/www/infrastructure/000-default.conf /etc/apache2/sites-enabled/000-default.conf

RUN cp -rf /var/www/infrastructure/start.sh /usr/local/bin/start

RUN chmod -R 777 /usr/local/bin/start

RUN chown -R www-data:www-data /var/www

RUN a2enmod rewrite

CMD ["/usr/local/bin/start"]
