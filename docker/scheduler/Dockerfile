FROM registry.gitlab.com/coderslab/docker/phpfpm-cron:8.1

# Permission fix
RUN usermod -u 1002 www-data

RUN echo "" > /var/log/cron.log
RUN chmod 0777 /var/log/cron.log
COPY ./entrypoint.sh /

CMD /entrypoint.sh
#CMD ["sh","-c","chmod 0600 /var/spool/cron/crontabs/www-data && chown www-data:crontab /var/spool/cron/crontabs/www-data && service cron restart && tail -f /var/log/cron.log"]
