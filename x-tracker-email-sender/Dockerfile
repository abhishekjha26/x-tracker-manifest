FROM php:8.0-apache

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update

RUN apt-get -y install libzip-dev zip unzip

RUN a2enmod rewrite

COPY . /var/www

WORKDIR /var/www

COPY start-apache /usr/local/bin

RUN chmod -R 777 /usr/local/bin

# RUN ["chmod", "+x", "/usr/local/bin"]

RUN chown -R www-data:www-data /var/www

CMD ["start-apache"]
