<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

$device = new Device($IP);

$db = new Database();

$db->mac_addr = $device->mac;

if( $db->get_device_id() == 0 ) {
  $db->add_device();
}

$total_mb_limit = $db->get_total_mb_limit();

$total_mb_used = $db->get_total_mb_used();

echo json_encode(['ip' => $device->ip, 'mac' => $device->mac, 'mb_limit' => $total_mb_limit, 'mb_used' => $total_mb_used]);
