FROM php:7.2-cli

RUN apt-get update && apt-get install -y git


# == MySQL extension == #
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql \
    && docker-php-ext-enable pdo pdo_mysql


# == PostgreSQL extension == #
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-enable pdo pdo_pgsql pgsql


# == Microsoft SQL Seriver extension == #
RUN apt-get -y --no-install-recommends install unixodbc-dev \
    && docker-php-ext-install mbstring pdo \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv


RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

RUN mkdir /var/www/anonymizer
WORKDIR /var/www/anonymizer
RUN composer require webnet-fr/database-anonymizer:master-dev symfony/console symfony/config symfony/yaml
