<?php
require_once '../lib/autoload.php';
$password = '../conf/password.sha256';

$txn = filter_input(INPUT_GET, 'txn');
$net = filter_input(INPUT_GET, 'net');
$dev = filter_input(INPUT_GET, 'dev');

if( $dev || $txn ) {
  $db = new Database();
}

if( !isset($_COOKIE['hash']) ) {
  http_response_code(401);
  exit;
}

if( $_COOKIE['hash'] !== file_get_contents($password) ) {
  http_response_code(401);
  exit;
}

if( $dev == 'all' ) {
  echo json_encode($db->get_devices(), JSON_PRETTY_PRINT);
}

if( $dev == 'active' ) {
  echo json_encode($db->get_active_devices(), JSON_PRETTY_PRINT);
}

if( $dev == 'restricted' ) {
  echo json_encode($db->get_restricted_devices());
}

if( $dev == 'recent' ) {
  echo json_encode($db->get_recent_devices(), JSON_PRETTY_PRINT);
}

if( $dev == 'add_session' ) {
  $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);
    $mac = filter_input(INPUT_GET, 'mac', FILTER_VALIDATE_MAC);

  if( !$limit ) exit;

  $db->set_mac($mac);
  $db->set_mb_limit($limit);

  if( !$db->get_device_id() ) {
    http_response_code(403);
    exit;
  }

  $db->add_session();

  list($mb_limit,$mb_used) = $db->get_data_usage();

  if( $mb_limit > $mb_used ) {
    $ipt = new Iptables($db->get_device_ip());
    $ipt->add_client();
  }

  echo json_encode(['devid' => $db->get_did(), 'sid' => $db->get_sid(), 'mb_limit' => $mb_limit, 'mb_used' => $mb_used]);
}

if( $dev == 'get_session' ) {
  $mac = filter_input(INPUT_GET, 'mac', FILTER_VALIDATE_MAC);

  $db->set_mac($mac);

  if( !$db->get_device_id() ) {
    http_response_code(403);
    exit;
  }

  $device = $db->get_device_info();

  list($mb_limit,$mb_used) = $db->get_data_usage();

  $ipt = new Iptables($db->get_device_ip());

  echo json_encode(['mac' => $device['mac'], 'ip' => $device['ip'], 'host' => $device['host'], 'mb_limit' => $mb_limit, 'mb_used' => $mb_used, 'active_at' => $db->get_active_at(), 'connected' => $ipt->connected()]);
}

if( $dev == 'get_txn' ) {
  $mac = filter_input(INPUT_GET, 'mac', FILTER_VALIDATE_MAC);

  $db->set_mac($mac);

  if( !$db->get_device_id() ) {
    http_response_code(403);
    exit;
  }

  echo json_encode($db->get_device_sessions(), JSON_PRETTY_PRINT);

}

if( $dev == 'del_txn' ) {
  $sid = filter_input(INPUT_GET, 'sid', FILTER_VALIDATE_INT);

  $db->set_sid($sid);
  $db->rem_session();
}

if( $dev == 'clear_mb' ) {
  $mac = filter_input(INPUT_GET, 'mac', FILTER_VALIDATE_MAC);

  $db->set_mac($mac);

  if( !$db->get_device_id() ) {
    http_response_code(403);
    exit;
  }

  $db->clear_mb();
}

if( $txn == 'get_all' ) {
  $o = filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT) ?: 0;
  $l = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 10;

  $db->set_offset($o);
  $db->set_limit($l);

  echo json_encode($db->get_all_txn(), JSON_PRETTY_PRINT);
}
