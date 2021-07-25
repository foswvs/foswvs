<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'autoload.php';

$ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$does = filter_input(INPUT_GET,'do');

$sess = new Sess($ip);
$help = new Helper();
$data = [];

if( $does == 'get_txn' ) {
  $sess->db->offset = filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT);
  $sess->db->limit  = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);

  if( !$sess->db->offset ) $sess->db->offset = 0;
  if( !$sess->db->limit )  $sess->db->limit = 10;

  echo json_encode($sess->db->get_device_sessions(), JSON_PRETTY_PRINT);
}

if( $does == 'topup' ) {
  $sess->topup();
}

if( $does == 'topup_cancel' ) {
  $sess->coinslot->sensor_off();
}

if( $does == 'connect' ) {
  $sess->iptables->add_client();
}

if( $does == 'session' ) {
  $data = [
    "initr" => $sess->initr,
    "timer" => $sess->get_timer(),
    "ip_addr" => $sess->device->ip,
    "mac_addr" => $sess->device->mac,
    "connected" => $sess->iptables->connected(),
    "insert_coin" => $sess->coinslot->sensor_read(),
    "mb_limit" => $sess->mb_limit,
    "mb_used" => $sess->mb_used,
    "total_mb_limit" => $sess->total_mb_limit,
    "total_mb_used" => $sess->total_mb_used];

  echo json_encode($data, JSON_PRETTY_PRINT);
}

