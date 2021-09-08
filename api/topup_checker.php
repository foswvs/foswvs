<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$MAC = Network::device_mac($IP);

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

$str = file_get_contents($file);
$obj = json_decode($str);

if( $obj === NULL ) {
  http_response_code(401);
  exit;
}

if( $obj->mac != $MAC ) {
  http_response_code(401);
  exit;
}

echo $str;