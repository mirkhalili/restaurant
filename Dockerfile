FROM php:8.3-apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN docker-php-ext-install pdo_mysql && a2enmod rewrite
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
