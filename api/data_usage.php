<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

$device = new Device($IP);

$db = new Database();

$db->mac_addr = $device->mac;

if( $db->get_device_id() == 0 ) {
  $db->ip_addr = $IP;
  $db->hostname = '-NA-';
  $db->add_device();
}

list($mb_limit,$mb_used) = $db->get_data_usage();

echo json_encode(['ip' => $IP, 'mac' => $device->mac, 'mb_limit' => $mb_limit, 'mb_used' => $mb_used]);
