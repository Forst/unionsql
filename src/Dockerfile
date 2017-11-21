FROM ubuntu:xenial
LABEL maintainer="forst at forstwoof.ru"

RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections && \
    echo 'mysql-server mysql-server/root_password password PWD_ROOT' | debconf-set-selections && \
    echo 'mysql-server mysql-server/root_password_again password PWD_ROOT' | debconf-set-selections && \
    apt-get update && \
    apt-get -y install mysql-server nginx php7.0-fpm php7.0-mysql supervisor && \
    rm -rf /var/www/* && \
    rm -f /etc/nginx/sites-available/default

COPY nginx-default /etc/nginx/sites-available/default
ADD www.tar /var/
COPY database.sql mysql_init.sh /tmp/
COPY mysql_start.sh /usr/bin/
COPY supervisord.conf /etc/

RUN chown -R www-data:www-data /var/www && \
    echo "daemon off;\n$(cat /etc/nginx/nginx.conf)" > /etc/nginx/nginx.conf && \
    mkdir -p /var/run/php && \
    chmod +x /tmp/mysql_init.sh /usr/bin/mysql_start.sh && \
    /tmp/mysql_init.sh && \
    rm /tmp/mysql_init.sh /tmp/database.sql

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
