#!/bin/bash

set -e

mkdir -p /var/run/mysqld
chown -R mysql:mysql /var/lib/mysql /var/run/mysqld

mysqld_safe &
mysql_pid=$!

until mysqladmin --user=root --password="PWD_ROOT" ping 2>/dev/null; do
    echo -n "."
    sleep 1
done

mysql --user=root --password="PWD_ROOT" < /tmp/database.sql

mysqladmin --user=root --password="PWD_ROOT" shutdown

wait $mysql_pid
