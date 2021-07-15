<?php
ini_set('display_errors', 1);

require_once __DIR__ . '/api/autoload.php';

$sess = new Session($_SERVER['REMOTE_ADDR']);
$help = new Helper();
$data = [];

if( isset($_GET['do']) ) {
  $action = $_GET['do'];

  switch($action) {
    case "topup":
      $sess->topup();
      $data = ['sid' => $sess->id, 'piso_count' => $sess->coinslot->piso_count];
      break;
    case "topup_cancel":
      $sess->coinslot->deactivate();
      $data = ['sid' => $sess->id];
      break;
    default:
      exit("use: topup/topup_cancel");
  }
}
else {
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
    "ping" => $sess->device->ping(),
    "sid" => $sess->id
   ];
}

echo json_encode($data, JSON_PRETTY_PRINT);
