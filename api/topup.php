<?php
set_time_limit(60);

require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

$device = new Device($IP);
$coinslot = new Coinslot();
$helper = new Helper();

if( !$device->mac ) {
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
    usleep(27000);
  }

  $timer = time() - $start;

  $diff = $wait - $timer;

  $mb = $helper->amt_to_mb($count);

  $log = ['mac' => $device->mac, 'amt' => $count, 'mb' => $mb, 'cd' => $diff];

  $json = json_encode($log);

  fseek($fp, 0);
  fwrite($fp, $json);
  ftruncate($fp, strlen($json));

  if( $timer >= $wait ) {
    $coinslot->sensor_off();
  }
}

fclose($fp);

if( $count ) {
  $db = new Database();

  $db->mac_addr = $device->mac;

  $db->mb_limit = $mb;
  $db->piso_count = $count;

  if( !$db->get_device_id() ) exit;

  $db->add_session();

  $ipt = new Iptables($IP);
  $ipt->add_client();
}
