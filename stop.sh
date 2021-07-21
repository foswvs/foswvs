#!/bin/bash
APPDIR=/home/pi/foswvs

sudo iptables-save > $APPDIR/conf/iptables_latest.txt

if [[ $(pidof dhcpd) ]]; then
  sudo systemctl stop isc-dhcp-server.service
fi

if [[ $(pidof nginx) ]]; then
  sudo systemctl stop nginx.service
fi

if [[ $(pidof php-fpm7.3) ]]; then
  sudo systemctl stop php7.3-fpm.service
fi
