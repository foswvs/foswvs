<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$db = new Database();

$db->set_ip($IP);

if( !$db->get_device_id_by_ip() ) {
  http_response_code(401);
  exit;
}

$MAC = $db->get_device_mac();

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

if( $obj === NULL ) exit;

if( $obj->mac != $MAC ) {
  http_response_code(401);
  exit;
}

$coinslot->sensor_off();
