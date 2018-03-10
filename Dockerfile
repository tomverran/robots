FROM ubuntu:bionic
RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get -y update && \
    apt-get -y install git php7.2-cli php7.2-dom php7.2-curl php7.2-zip php7.2-mbstring php7.2-xdebug


RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php && mv composer.phar /usr/local/bin/composer

RUN useradd --create-home robots
WORKDIR /home/robots
USER robots

ENTRYPOINT composer update && \
    php vendor/bin/phpunit -c phpunit.xml --coverage-clover build/logs/clover.xml

VOLUME /home/robots
