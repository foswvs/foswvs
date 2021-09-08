<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$db = new Database();

$db->set_ip($IP);

if( !$db->get_device_id_by_ip() ) {
  http_response_code(401);
  exit;
}

list($mb_limit,$mb_used) = $db->get_data_usage();

if( $mb_limit <= $mb_used ) {
  http_response_code(403);
  exit;
}

$ipt = new Iptables($IP);
$ipt->add_client();