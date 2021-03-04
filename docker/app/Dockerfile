FROM php:7.4-fpm-buster

RUN set -ex \
    && apt-get update \
    && apt-get install -y zlib1g zlib1g-dev libzip-dev supervisor git \
    && docker-php-ext-install -j$(nproc) mysqli pdo pdo_mysql zip

# install composer
RUN apt-get install -y wget \
 && wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- \
 && mv composer.phar /usr/local/bin/composer

CMD /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
WORKDIR /var/www/

RUN set -ex \
    && echo "xdebug.idekey = PHPSTORM" >> /usr/local/etc/php/conf.d/20-xdebug.ini \
    && echo "xdebug.default_enable=0" >> /usr/local/etc/php/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_port = 9001" >> /usr/local/etc/php/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_handler = dbgp" >> /usr/local/etc/php/conf.d/20-xdebug.ini \
# your local ip
    && echo "xdebug.remote_host = 192.168.0.101" >> /usr/local/etc/php/conf.d/20-xdebug.ini \
    && echo "access.log = /var/log/php/access.log" >> /usr/local/etc/php/conf.d/30-logging.ini \
    && echo "error.log = /var/log/php/error.log" >> /usr/local/etc/php/conf.d/30-logging.ini \
    && echo "error_log = /var/log/php/error.log" >> /usr/local/etc/php/conf.d/30-logging.ini \
    && echo "log_errors = 1" >> /usr/local/etc/php/conf.d/30-logging.ini \
    && echo 'memory_limit = 1024M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini \
    && echo 'post_max_size = 25M' >> /usr/local/etc/php/conf.d/docker-post-max-size.ini \
    && echo 'upload_max_filesize = 25M' >> /usr/local/etc/php/conf.d/docker-upload-max-filesize.ini

COPY www.conf /usr/local/etc/php-fpm.d/

ARG UID=1000
ARG GID=1000

RUN usermod -u $UID www-data && groupmod -g $GID www-data;

# forward request and error logs to docker log collector
RUN set -ex \
    && mkdir /var/log/php \
    && touch /var/log/php/access.log \
    && touch /var/log/php/error.log \
    && ln -sf /dev/stdout /var/log/php/access.log \
    && ln -sf /dev/stderr /var/log/php/error.log

# enable/disable xdebug
RUN echo "xdebug.remote_autostart = true" >> /usr/local/etc/php/conf.d/20-xdebug.ini
