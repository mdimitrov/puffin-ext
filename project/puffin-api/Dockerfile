FROM richarvey/nginx-php-fpm:php5

WORKDIR /puffin-api

ADD composer.json composer.lock /tmp/

RUN cd /tmp && composer install && cp -a /tmp/vendor /puffin-api

COPY . /puffin-api/

COPY default.conf /etc/nginx/sites-available/default.conf
