<?php
require 'api/autoload.php';

$bytes = 0;

$bytes_cap = 0;
$bytes_used = 0;

if( isset($_GET['ip']) ) {
  $db = new Database();
  $device = new Device($_GET['ip']);
  $iptables = new Iptables($device->ip, $device->mac);

  if( $db->get_device_id() ) {
    $bytes = $iptables->bytes_used();

    $db->set_mb_credit($bytes);

    $bytes_cap = $db->get_total_mb_credit();
    $bytes_used = $db->get_total_mb_used();

    if( $bytes_cap <= $bytes_used ) {
      $iptables->rm_client();
    }
  }
}

echo json_encode(['bytes_cap' => $bytes_cap, 'bytes_used' => $bytes_used]);
