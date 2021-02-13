FROM php:7.4-apache

RUN apt-get update
RUN apt-get upgrade -y
#####>
# ENV HTTP_PROXY="http://127.0.0.1:4873"
#####>
RUN apt install -y apt-utils mariadb-client curl
RUN apt-get -qq -y install zlib1g-dev libzip-dev sendmail libsodium-dev vim git zip unzip wget libsqlite3-dev libsqlite3-0 libbz2-dev
RUN docker-php-ext-install pdo pdo_mysql mysqli bz2 opcache pdo_sqlite
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN echo "sendmail_path=/usr/sbin/sendmail -t -i" >> /usr/local/etc/php/conf.d/sendmail.ini
RUN mv $HOME/.symfony/bin/symfony /usr/local/bin/symfony
#####>
COPY ./www ./
#####>
RUN sed -i '/#!\/bin\/sh/aservice sendmail restart' /usr/local/bin/docker-php-entrypoint
RUN sed -i '/#!\/bin\/sh/aecho "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint
####>
COPY ./.conf/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf
# COPY ./.conf/apache2/mods-enabled/proxy.conf /etc/apache2/mods-enabled/proxy.conf
COPY ./.conf/php/php.ini /usr/local/etc/php/php.ini
RUN a2enmod rewrite proxy proxy_http proxy_wstunnel proxy_balancer lbmethod_byrequests
RUN /etc/init.d/apache2 restart
EXPOSE 80 3306
