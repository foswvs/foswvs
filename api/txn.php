<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

$dev = new Device($IP);

$db = new Database();

$db->mac_addr = $dev->mac;

if( $db->get_device_id() ==0 ) exit;

echo json_encode($db->get_device_sessions(), JSON_PRETTY_PRINT);
