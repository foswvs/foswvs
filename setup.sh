#!/bin/bash
sudo cp conf/dhcpd.conf /etc/dhcp/dhcpd.conf
sudo cp conf/interfaces /etc/network/interfaces
sudo cp conf/nginx.conf /etc/nginx/nginx.conf

echo "dhcpd.conf completed!"
echo "interfaces completed!"
echo "nginx completed!"

./iptables.sh
