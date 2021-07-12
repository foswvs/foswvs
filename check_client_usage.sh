#!/bin/bash

while true
do
  for ip in $(arp -an|grep -oE '10.0.[0-9]{1,3}.[0-9]{1,3}');
  do
    curl -s "http://127.0.0.1/check_data_usage.php?ip=$ip" -o /dev/null
    sleep 3
  done
  sleep 3
done
