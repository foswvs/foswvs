<?php
error_reporting(E_ALL);
set_time_limit(60);

require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$db = new Database();

$db->set_ip($IP);

if( !$db->get_device_id_by_ip() ) {
  http_response_code(401);
  exit;
}

$MAC = $db->get_device_mac();

if( $db->get_topup_count() > 5 ) {
  http_response_code(429);
  exit;
}

$coinslot = new Coinslot();

if( !filter_var($MAC, FILTER_VALIDATE_MAC) ) {
  http_response_code(401);
  exit;
}

if( $coinslot->sensor_read() ) {
  http_response_code(401);
  exit;
}

$mb = 0;
$wait = 50;
$data = [];
$count = 0;
$start = time();

$fp = fopen('/tmp/coinslot','w');

$coinslot->sensor_on();

while( $coinslot->sensor_read() ) {
  if( $coinslot->slot_read() ) {
    $count++;
    usleep(22000);
  }

  $timer = time() - $start;

  $diff = $wait - $timer;

  $mb = Helper::amount_mb($count);

  $log = ['mac' => $MAC, 'amt' => $count, 'mb' => $mb, 'cd' => $diff];

  $json = json_encode($log);

  fseek($fp, 0);
  fwrite($fp, $json);
  ftruncate($fp, strlen($json));

  if( $timer >= $wait ) {
    $coinslot->sensor_off();
  }
}

fclose($fp);

if( $count === 0 ) {
  $db->set_topup_count();
  http_response_code(402);
  exit;
}

$db->set_amount($count);
$db->set_mb_limit($mb);

$db->add_session();

$ipt = new Iptables($IP);
$ipt->add_client();
