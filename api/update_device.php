<?php
require 'autoload.php';

$network = new Network();

$devices = $network->dhcp_leases();

foreach($devices as $device) {
  $db->mac_addr   = $device['mac'];
  $db->ip_addr    = $device['ip'];
  $db->hostname   = $device['host'];
  $db->updated_at = $device['begin'];

  $db->update_device();
}
