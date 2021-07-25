#!/usr/bin/php
<?php
require __DIR__ . '/../lib/autoload.php';

$ip = $argv[1];
$mac = $argv[2];
$host = $argv[3];
$date = date('Y-m-d H:i:s');

if( !filter_var($mac, FILTER_VALIDATE_MAC) ) {
  $dev = new Device($ip);
  $mac = $dev->get_mac();
}

if( filter_var($ip, FILTER_VALIDATE_IP) && filter_var($mac, FILTER_VALIDATE_MAC) ) {
  $db = new Database();
  $ipt = new Iptables($ip, $mac);

  $db->mac_addr = strtoupper($mac);
  $db->ip_addr  = $ip;
  $db->hostname = $host;

  $db->updated_at = $date;

  if( !$db->get_device_id() ) {
    $db->add_device();
  }

  $db->update_device();

  if( $db->get_total_mb_limit() > $db->get_total_mb_used() ) {
    $ipt->add_client();
  }
  else {
    $ipt->rem_client();
  }
}
