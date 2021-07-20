<?php
ini_set('display_errors', 1);

require_once __DIR__ . '/api/autoload.php';

$ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$does = filter_input(INPUT_GET,'do');

$sess = new Session($ip);
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
  $sess->coinslot->deactivate();
}

if( $does == 'session' ) {
  $data = [
    "initr" => $sess->initr,
    "ip_addr" => $sess->device->ip,
    "mac_addr" => $sess->device->mac,
    "device_id" => $sess->device->id,
    "connected" => $sess->iptables->connected(),
    "insert_coin" => $sess->coinslot->slot_state,
    "piso_count" => $sess->piso_count,
    "mb_limit" => $help->format_mb($sess->mb_limit),
    "mb_used" => $sess->mb_used,
    "total_mb_limit" => $help->format_mb($sess->total_mb_limit),
    "total_mb_used" => $help->format_mb($sess->total_mb_used),
    "ping" => rand(1,20),
    "sid" => $sess->id
   ];

  echo json_encode($data, JSON_PRETTY_PRINT);
}

