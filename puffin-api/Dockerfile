FROM richarvey/nginx-php-fpm:php5

COPY . /puffin-api/
RUN cd puffin-api && composer install
COPY default.conf /etc/nginx/sites-available/default.conf

