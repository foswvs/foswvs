<?php
ini_set('display_errors', 1);

require './device.php';

$ip_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

$device = new Device($ip_addr);

$data = [];

if( isset($_GET['do']) ) {
  $action = $_GET['do'];

  switch($action) {
    case "topup":
      $device->topup();
      break;
    case "cancel":
      $device->deactivate();
      break;
    default:
      exit('topup/cancel');
  }
}
else {
  $data = [
    "ip_addr" => $device->ip_addr,
    "mac_addr" => $device->mac_addr,
    "device_id" => $device->device_id,
    "insert_coin" => $device->state(),
    "piso_count" => $device->piso_count,
    "mb_credit" => $device->mb_credit,
    "mb_used" => $device->mb_used,
    "ping" => 1,
   ];
}

echo json_encode($data, JSON_PRETTY_PRINT);

