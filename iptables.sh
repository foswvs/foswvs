#!/bin/bash
sudo iptables -F
sudo iptables -t nat -F

sudo iptables -A FORWARD -s 10.0.0.0/20 -p tcp --dport 53 -j ACCEPT
sudo iptables -A FORWARD -s 10.0.0.0/20 -p udp --dport 53 -j ACCEPT
sudo iptables -A FORWARD -s 10.0.0.0/20 -p tcp --dport 80 -d 10.0.0.1 -j ACCEPT
sudo iptables -A FORWARD -s 10.0.0.0/20 -j DROP
sudo iptables -t nat -A PREROUTING -s 10.0.0.0/20 -p tcp --dport 80 -j DNAT --to-destination 10.0.0.1
sudo iptables -t nat -A POSTROUTING -j MASQUERADE
