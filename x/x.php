<?php
require_once '../api/autoload.php';

$device = filter_input(INPUT_GET, 'device');
$network = filter_input(INPUT_GET, 'network');

if( $device ) {
  $help = new Helper();
}

if( $network == 'dhcp_leases') {
  $net = new Network();

  echo json_encode($net->dhcp_leases(), JSON_PRETTY_PRINT);
}

if( $device == 'add_session' ) {
  $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);
    $mac = filter_input(INPUT_GET, 'mac', FILTER_VALIDATE_MAC);

  if( $limit < 1 ) exit;

  if( empty($mac) ) exit;

  $db = new Database();

  $db->mac_addr = $mac;
  $db->get_device_id();
  $db->add_session();
  $db->set_mb_limit($limit);
  $db->set_device_session();

  $total_mb_limit = $help->format_mb($db->get_total_mb_limit());
  $total_mb_used = $help->format_mb($db->get_total_mb_used());

  echo json_encode(['devid' => $db->devid, 'sid' => $db->sid, 'total_mb_limit' => $total_mb_limit, 'total_mb_used' => $total_mb_used]);
}

if( $device == 'get_session' ) {
  $mac = filter_input(INPUT_GET, 'mac', FILTER_VALIDATE_MAC);

  if( empty($mac) ) exit;

  $db = new Database();

  $db->mac_addr = $mac;

  $db->get_device_id();
  $db->get_device_session();

  $total_mb_used = $help->format_mb($db->get_total_mb_used());
  $total_mb_limit = $help->format_mb($db->get_total_mb_limit());

  echo json_encode(['devid' => $db->devid, 'sid' => $db->sid, 'mac' => $db->mac_addr, 'total_mb_limit' => $total_mb_limit, 'total_mb_used' => $total_mb_used]);
}
