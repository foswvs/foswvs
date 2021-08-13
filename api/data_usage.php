<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$db = new Database();

$db->ip_addr = $IP;

if( !$db->get_device_id_by_ip() ) {
  http_response_code(401);
  exit;
}

$MAC = $db->get_device_mac();

list($mb_limit,$mb_used) = $db->get_data_usage();

echo json_encode(['ip' => $IP, 'mac' => $MAC, 'mb_limit' => $mb_limit, 'mb_used' => $mb_used]);
