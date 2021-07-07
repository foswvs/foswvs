<?php
require 'api/autoload.php';

if( isset($_SERVER['REMOTE_ADDR']) ) {
  $device = new Device($_SERVER['REMOTE_ADDR']);
  $iptables = new Iptables($device->ip, $device->mac);

}

echo json_encode(['mac' => $device->mac, 'ping' => $device->ping(), 'connected' => $iptables->connected()]);
