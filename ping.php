<?php
require 'api/autoload.php';

$mac = "XX:XX:XX:XX:XX:XX";
$ping = 0;
$connected = false;

if( isset($_SERVER['REMOTE_ADDR']) ) {
  $device = new Device($_SERVER['REMOTE_ADDR']);
  $iptables = new Iptables($device->ip, $device->mac);

  $mac = $device->mac;
  $ping = $device->ping();
  $connected = $iptables->connected();
}

echo json_encode(['mac' => $mac, 'ping' => $ping, 'connected' => $connected]);
