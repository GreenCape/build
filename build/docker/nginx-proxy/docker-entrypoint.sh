#!/bin/bash
set -e

if [ "${1:0:1}" = '-' ]; then
	set -- nginx "$@"
fi

if [ "$1" = 'nginx' ]; then

    echo "Setting IP address of Apache container (${WEB_APACHE_PORT_80_TCP_ADDR})"
    sed -i "s/APACHE_IP/${WEB_APACHE_PORT_80_TCP_ADDR}/" /etc/nginx/conf.d/*
    echo "Setting IP address of Nginx container (${WEB_NGINX_PORT_80_TCP_ADDR})"
    sed -i "s/NGINX_IP/${WEB_NGINX_PORT_80_TCP_ADDR}/" /etc/nginx/conf.d/*
fi

exec "$@"
