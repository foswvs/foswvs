#!/bin/bash
APPDIR=/home/pi/foswvs

sudo cp $APPDIR/conf/dhcpd.conf /etc/dhcp/dhcpd.conf
sudo cp $APPDIR/conf/nginx.conf /etc/nginx/nginx.conf
sudo cp $APPDIR/conf/interfaces /etc/network/interfaces

sudo sysctl net.ipv4.ip_forward=1

sudo iptables-restore < $APPDIR/conf/iptables.txt

$APPDIR/arp_check.sh
