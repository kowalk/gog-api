FROM registry.gitlab.com/coderslab/docker/phpfpm:8.1

RUN apt-get update --fix-missing
RUN apt-get upgrade -y
RUN apt-get install -y supervisor

# Permission fix
RUN usermod -u 1000 www-data

RUN chmod a+rwx /var/log/supervisor/

CMD ["/usr/bin/supervisord"]
