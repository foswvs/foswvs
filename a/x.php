<?php
require_once '../api/autoload.php';

$txn = filter_input(INPUT_GET, 'txn');
$net = filter_input(INPUT_GET, 'net');
$dev = filter_input(INPUT_GET, 'dev');

$hash = trim(file_get_contents('password.sha256'));

session_start();

if( $net == 'login' ) {
  $_SESSION['hash'] = hash('sha256',filter_input(INPUT_POST, 'password'));
}

if( !isset($_SESSION['hash']) ) {
  session_destroy();
  http_response_code(401);
  exit;
}

if( $_SESSION['hash'] !== $hash ) {
  session_destroy();
  http_response_code(401);
  exit;
}

if( $net == 'chpwd' ) {
  file_put_contents('password.sha256', hash('sha256',filter_input(INPUT_POST, 'password')));
  session_destroy();
  http_response_code(200);
  exit;
}

if( $dev || $txn ) {
  $db = new Database();
  $help = new Helper();
}

if( $net == 'dhcp_leases') {
  $network = new Network();

  echo json_encode($network->dhcp_leases(), JSON_PRETTY_PRINT);
}

if( $dev == 'all' ) {
  echo json_encode($db->get_devices(), JSON_PRETTY_PRINT);
}

if( $dev == 'add_session' ) {
  $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);
    $mac = filter_input(INPUT_GET, 'mac');

  if( $limit < 1 ) exit;

  if( empty($mac) ) exit("provide mac address");

  $db->mac_addr = $mac;
  $db->mb_limit = $limit;

  $db->get_device_id();

  if(!$db->devid) exit("mac address doesn't exist.");

  $db->add_session();

  $db->set_mb_limit();
  $db->set_device_sid();

  $total_mb_limit = $help->format_mb($db->get_total_mb_limit());
  $total_mb_used = $help->format_mb($db->get_total_mb_used());

  echo json_encode(['devid' => $db->devid, 'sid' => $db->sid, 'total_mb_limit' => $total_mb_limit, 'total_mb_used' => $total_mb_used]);
}

if( $dev == 'get_session' ) {
  $mac = filter_input(INPUT_GET, 'mac');

  if( empty($mac) ) exit("provide mac address");

  $db->mac_addr = $mac;

  $db->get_device_id();

  if(!$db->devid) exit("mac address doesn't exist.");

  $db->get_device_sid();

  $device = $db->get_device_info();

  $total_mb_used = $help->format_mb($db->get_total_mb_used());
  $total_mb_limit = $help->format_mb($db->get_total_mb_limit());

  echo json_encode(['mac' => $device['mac'], 'ip' => $device['ip'], 'host' => $device['host'], 'total_mb_limit' => $total_mb_limit, 'total_mb_used' => $total_mb_used]);
}

if( $dev == 'clear_mb' ) {
  $mac = filter_input(INPUT_GET, 'mac');

  if( empty($mac) ) exit("provide mac address");

  $db->mac_addr = $mac;

  $db->get_device_id();

  if(!$db->devid) exit("mac address doesn't exist.");

  $db->clear_mb();
}

if( $txn == 'get_all' ) {
  $db->offset = filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT);
  $db->limit  = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);

  if( !$db->offset ) $db->offset = 0;
  if( !$db->limit )  $db->limit = 10;

  echo json_encode($db->get_all_txn(), JSON_PRETTY_PRINT);
}
