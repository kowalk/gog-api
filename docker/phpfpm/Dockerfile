FROM php:8.3-fpm

RUN apt-get update --fix-missing \
    && apt-get install -y libpng-dev git libicu-dev zlib1g-dev default-mysql-client httpie jq nano ssh gnupg2 unzip libzip-dev wget tar libfontconfig1 libxrender1 \
    libxml2-dev libjpeg-dev librabbitmq-dev libssh-dev libc-client-dev libkrb5-dev liblz4-dev libzstd-dev locales locales-all

ENV LC_ALL pl_PL.UTF-8
ENV LANG pl_PL.UTF-8
ENV LANGUAGE pl_PL.UTF-8

RUN docker-php-ext-install intl opcache zip pdo pdo_mysql soap bcmath sockets \
    && yes | pecl install xdebug \
           && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
           && echo "xdebug.mode = develop,debug,profile" >> /usr/local/etc/php/conf.d/xdebug.ini \
           && echo "xdebug.start_with_request = trigger" >> /usr/local/etc/php/conf.d/xdebug.ini \
           && echo "xdebug.start_upon_error = yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
           && echo "xdebug.discover_client_host = true" >> /usr/local/etc/php/conf.d/xdebug.ini \
           && echo "xdebug.client_port = 9000" >> /usr/local/etc/php/conf.d/xdebug.ini \
           && echo "xdebug.max_nesting_level = 512" >> /usr/local/etc/php/conf.d/xdebug.ini \
           && echo "xdebug.idekey = PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini \
           && echo "xdebug.output_dir = "/usr/share/nginx/html/var"" >> /usr/local/etc/php/conf.d/xdebug.ini \
           && echo "xdebug.profiler_output_name = "cachegrind.out.%p"" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && docker-php-ext-enable intl opcache zip xdebug
RUN yes | pecl install igbinary msgpack && docker-php-ext-enable igbinary msgpack
RUN yes | pecl install -f redis && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && composer self-update
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && apt install symfony-cli

RUN echo 'pm.max_children = 15' >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo 'pm.max_requests = 500' >> /usr/local/etc/php-fpm.d/zz-docker.conf

