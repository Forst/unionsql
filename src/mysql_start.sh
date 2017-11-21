#!/bin/bash

set -e

chown -R mysql:mysql /var/lib/mysql /var/run/mysqld
mysqld_safe
