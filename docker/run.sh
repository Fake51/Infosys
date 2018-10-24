#!/bin/bash

/usr/sbin/service nginx start
/usr/sbin/service php5.6-fpm start
/usr/sbin/service mysql start

if [ ! -f /var/www/infosys/composer.phar ]
then
    cd /var/www/infosys
    /usr/local/bin/composer-installer.sh
fi

if [ -f /var/www/create.sql ]
then
    /usr/bin/mysql -u root < /var/www/create.sql
    rm /var/www/create.sql
fi

shutdown() {
	/usr/sbin/service php5.6-fpm stop
    /usr/sbin/service nginx stop
    /usr/sbin/service mysql stop
}

trap shutdown INT TERM

while true
do
    sleep 1
done
