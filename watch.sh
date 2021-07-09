#!/bin/bash

watch -n1 sudo iptables -vnL FORWARD --line-numbers
