FROM ubuntu:18.04

RUN apt update \
    && apt install -y software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt update \
    && echo "Europe/Copenhagen" > /etc/timezone \
    && DEBIAN_FRONTEND=noninteractive apt install -y \
    nginx-full \
    php5.6-cli \
    php5.6-curl \
    php5.6-fpm \
    php5.6-gd \
    php5.6-json \
    php5.6-mbstring \
    php5.6-mysql \
    php5.6-opcache \
    php5.6-sqlite3 \
    php5.6-tidy \
    php5.6-xml \
    php5.6-zip \
    mysql-server \
    wget

RUN mkdir -p /var/www/ \
    && rm /etc/php/5.6/fpm/pool.d/www.conf \
    && rm /etc/nginx/sites-enabled/default

COPY run.sh /usr/local/bin
COPY composer-installer.sh /usr/local/bin/
COPY create.sql /var/www/

CMD ["/usr/local/bin/run.sh"]
