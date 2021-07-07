#!/bin/bash
# sudo cp iptables.sh /etc/init.d/iptables-foswvs
# sudo update-rc.d iptables-foswvs defaults

# clear iptables entry
sudo iptables -F
sudo iptables -t nat -F
# set defaults
sudo iptables -t nat -A PREROUTING -s 10.0.0.0/20 -p tcp --dport 80 -j DNAT --to-destination 10.0.14.22

sudo iptables -A FORWARD -s 10.0.0.0/20 -p tcp --dport 80 -d 10.0.14.22 -j ACCEPT

# set the default forward policy drop
sudo iptables -P FORWARD DROP

sudo iptables -t nat -A POSTROUTING -j MASQUERADE
