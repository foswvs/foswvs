#!/usr/bin/php
<?php
require 'autoload.php';

$ip = $argv[1];
$mac = $argv[2];
$host = $argv[3];
$date = date('Y-m-d H:i:s');

if( $ip && $mac ) {
  $db = new Database();

  $db->mac_addr = strtoupper($mac);
  $db->ip_addr  = $ip;
  $db->hostname = $host;

  $db->updated_at = $date;

  file_put_contents('device.log',json_encode([$mac,$ip,$host,$date]) . PHP_EOL, FILE_APPEND);
  if( !$db->get_device_id() ) {
    $db->add_device();
  }

  $db->update_device();
}
echo "done." . PHP_EOL;
