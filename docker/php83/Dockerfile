FROM php:8.3-fpm

RUN apt-get update && apt-get install -y

# Copy php.ini file
# I suspect the PHP extensons modify the php.ini file
COPY php.ini /usr/local/etc/php/php.ini

# Install PHP extensions
RUN pecl install xdebug-3.3.1 \
    && docker-php-ext-enable xdebug

# Install mysqli
RUN docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable pdo pdo_mysql

# Mail
RUN apt-get update && \
    apt-get install -y msmtp && \
    apt-get clean

COPY msmtp.conf /etc/msmtprc

# PHP mail settings
RUN echo 'sendmail_path = "/usr/bin/msmtp -t"' > /usr/local/etc/php/conf.d/mail.ini