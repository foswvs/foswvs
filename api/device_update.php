<?php
require 'autoload.php';

$ip = filter_input(INPUT_GET, 'ip', FILTER_VALIDATE_IP);
$mac = filter_input(INPUT_GET, 'mac', FILTER_VALIDATE_MAC);
$host = filter_input(INPUT_GET, 'host');

if( $ip && $mac ) {
  $db = new Database();

  $db->mac_addr = strtoupper($mac);
  $db->ip_addr  = $ip;
  $db->hostname = $host;

  if( !$db->get_device_id() ) {
    $db->add_device();
  }

  $db->update_device();
}
