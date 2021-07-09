#!/bin/bash

echo "setting dhcpd.conf"
sudo cp conf/dhcpd.conf /etc/dhcp/dhcpd.conf

echo "setting interfaces"
sudo cp conf/interfaces /etc/network/interfaces

echo "setting nginx.conf"
sudo cp conf/nginx.conf /etc/nginx/nginx.conf

echo "configuration completed"
echo "setting iptables"
./iptables.sh

sudo sysctl net.ipv4.ip_forward=1
