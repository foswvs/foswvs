<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

$device = new Device($IP);

if( !$device->mac ) {
  http_response_code(401);
  exit;
}

$db = new Database();

$db->mac_addr = $device->mac;

if( $db->get_device_id() == 0 ) {
  http_response_code(401);
  exit;
}

if( $db->get_total_mb_limit() > $db->get_total_mb_used() ) {
  $ipt = new Iptables($device->ip, $device->mac);
  $ipt->add_client();
}
