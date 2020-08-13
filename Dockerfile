FROM php:7.2-apache

RUN apt-get update && apt-get install -y zip && apt-get install -y mariadb-client && apt-get install -y npm

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

RUN php composer-setup.php

RUN php -r "unlink('composer-setup.php');"

RUN docker-php-ext-install bcmath 

RUN docker-php-ext-install ctype 

RUN docker-php-ext-install json 

RUN docker-php-ext-install mbstring 

RUN docker-php-ext-install pdo 

RUN docker-php-ext-install pdo_mysql

RUN docker-php-ext-install tokenizer 

RUN docker-php-ext-install sockets

RUN a2enmod rewrite

WORKDIR /var/www/html
