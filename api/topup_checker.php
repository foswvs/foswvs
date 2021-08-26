<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

$device = new Device($IP);
$coinslot = new Coinslot();

if( !$coinslot->sensor_read() ) {
  http_response_code(401);
  exit;
}

$file = '/tmp/coinslot';

if( !file_exists($file) ) {
  http_response_code(401);
  exit;
}

$data = file_get_contents($file);

$data = json_decode($data, true);

if( $data === NULL ) {
  http_response_code(401);
  exit;
}

if( $data['mac'] != $device->mac ) {
  http_response_code(401);
  exit;
}

$data = json_encode($data);

echo $data;
