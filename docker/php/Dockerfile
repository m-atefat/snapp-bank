FROM  php:8.2-fpm

ARG USER
ARG GROUP
ARG UID
ARG GID

# Set Timezone
RUN ln -snf /usr/share/zoneinfo/Asia/Tehran /etc/localtime \
	&& echo Asia/Tehran > /etc/timezone

# Install PHP Extensions
RUN apt-get update -y \
	&& apt-get upgrade -y \
	&& apt-get install -y nano default-mysql-client libzip-dev unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev supervisor \
    && docker-php-ext-install zip exif  sockets  bcmath  pdo pdo_mysql  mysqli  opcache -j$(nproc) gd pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN groupadd -g $GID $GROUP \
    && useradd -m -g $GROUP -u $UID -s /bin/sh $USER \
    && chown -R $USER:$GROUP /var/www \
    && usermod -u $UID $USER \
    && groupmod -g $GID $USER

# Install Composer.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Supervisor
RUN  mkdir -p /var/log/supervisor && mkdir -p /etc/supervisor/conf.d
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

#  PHP jit
RUN echo 'opcache.enable_cli=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.enable=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.jit_buffer_size=256M' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.jit=tracing' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'file_uploads = On' >> /usr/local/etc/php/conf.d/upload.ini \
    && echo 'memory_limit = 1024M' >> /usr/local/etc/php/conf.d/upload.ini \
    && echo 'upload_max_filesize = 100M' >> /usr/local/etc/php/conf.d/upload.ini \
    && echo 'post_max_size = 150M' >> /usr/local/etc/php/conf.d/upload.ini
