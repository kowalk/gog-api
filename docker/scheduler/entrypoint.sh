#!/bin/bash

cp /crontab-www /var/spool/cron/crontabs/www-data
crontab -u www-data /var/spool/cron/crontabs/www-data
service cron restart
tail -f /var/log/cron.log
