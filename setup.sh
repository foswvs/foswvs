#!/bin/bash
APPDIR=/home/pi/foswvs

sudo cp $APPDIR/conf/dhcpd.conf /etc/dhcp/dhcpd.conf
sudo cp $APPDIR/conf/nginx.conf /etc/nginx/nginx.conf
sudo cp $APPDIR/conf/interfaces /etc/network/interfaces

sudo sysctl net.ipv4.ip_forward=1
#echo "1" > /proc/sys/net/ipv4/ip_forward

$APPDIR/iptables.sh
$APPDIR/check_client_usage.sh

