#!/bin/bash
APPDIR=/home/pi/foswvs

sudo cp $APPDIR/conf/dhcpd.conf /etc/dhcp/dhcpd.conf
sudo cp $APPDIR/conf/nginx.conf /etc/nginx/nginx.conf
sudo cp $APPDIR/conf/interfaces /etc/network/interfaces

sudo iptables-restore < $APPDIR/conf/iptables.txt

if [[ $(cat /proc/sys/net/ipv4/ip_forward) == 0 ]]; then
  sudo sysctl net.ipv4.ip_forward=1
fi

sleep 30

if [[ -z $(pidof dhcpd) ]]; then
  sudo systemctl start isc-dhcp-server.service
fi

if [[ -z $(pidof nginx) ]]; then
  sudo systemctl start nginx.service
fi

if [[ -z $(pidof php-fpm7.3) ]]; then
  sudo systemctl start php7.3-fpm.service
fi

while true
do
  /home/pi/foswvs/api/clients.php > /dev/null
  sleep 3
done
