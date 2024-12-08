FROM redis:alpine

RUN echo vm.overcommit_memory=1 >> /etc/sysctl.conf && sysctl -p
RUN echo http://dl-2.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories
RUN apk --no-cache add shadow && usermod -u 1002 redis
RUN usermod -g root redis
RUN chown -R redis:redis /data
RUN chmod -R 777 /data

USER redis
