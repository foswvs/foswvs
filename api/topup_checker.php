<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

$device = new Device($IP);
$coinslot = new Coinslot();

$file = '/tmp/coinslot';

$data = file_get_contents($file);

$data = json_decode($data, true);

if( $data === NULL ) {
  $data = ['amt' => 0, 'cd' => 0, 'mac' => '', 'mb' => 0, 'i' => 0];
}

$data['i'] = 1;

if( $data['mac'] != $device->mac ) {
  http_response_code(401);
  $data = ['mac' => $device->mac, 'amt' => 0, 'cd' => 0,'mb' => 0, 'i' => 0];
}

$data = json_encode($data);

echo $data;
