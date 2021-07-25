<?php
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
$wait = 30;
$data = [];
$count = 0;
$start = time();

$fp = fopen('/tmp/coinslot','w');

$coinslot->sensor_on();

while( $coinslot->sensor_read() ) {
  if( $coinslot->slot_read() ) {
    $count++;
    usleep(30000);
  }

  $timer = time() - $start;

  $diff = $wait - $timer;

  $mb = $helper->amt_to_mb($count);

  $data = json_encode(['mac' => $device->mac, 'amt' => $count, 'mb' => $mb, 'cd' => $diff]);

  fseek($fp,0);
  fwrite($fp, $data);
  ftruncate($fp, strlen($data));

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

  if( $db->get_device_id() == 0 ) exit;

  $db->add_session();
  $db->set_device_sid();

  $ipt = new Iptables($device->ip, $device->mac);
  $ipt->add_client();
}
