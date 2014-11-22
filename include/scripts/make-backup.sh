#!/bin/bash
DATE=`/bin/date +%Y%m%d-%H:%M:%S`
/usr/bin/mysqldump -u fastaval -p3=C,1t.wYlTJ -h db infosys2013 > /var/www/infosys/include/backup/$DATE.sql
