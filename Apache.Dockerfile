FROM php:8.0-apache
WORKDIR /var/www/html

#Install Memcached PHP extension
RUN apt-get update && apt-get install -y libz-dev libmemcached-dev && rm -r /var/lib/apt/lists/*
RUN pecl install memcached
RUN echo extension=memcached.so >> /usr/local/etc/php/conf.d/memcached.ini

#Copy Source Code to container
COPY src/ /var/www/html/

#Set Apache server configuration
EXPOSE 8080:80
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && a2enmod rewrite && service apache2 restart
