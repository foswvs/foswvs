<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$db = new Database();

$db->ip_addr = $IP;

if( $db->get_device_id_by_ip() == 0 ) exit;

echo json_encode($db->get_device_sessions(), JSON_PRETTY_PRINT);
