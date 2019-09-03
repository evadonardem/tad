FROM php:7.2-apache

RUN apt-get update && apt-get install -y zip

RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"

RUN php -r "if (hash_file('sha384', '/tmp/composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('/tmp/composer-setup.php'); } echo PHP_EOL;"

RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN php -r "unlink('/tmp/composer-setup.php');"

RUN docker-php-ext-install sockets

WORKDIR /var/www/html