<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$db = new Database();

$db->ip_addr = $IP;

if( $db->get_device_id_by_ip() == 0 ) {
  http_response_code(401);
  exit;
}

list($mb_limit,$mb_used) = $db->get_data_usage();

if( $mb_limit > $mb_used ) {
  $ipt = new Iptables($IP);
  $ipt->add_client();
  echo 'connected';
}
