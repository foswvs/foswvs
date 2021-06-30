<?php
ini_set('display_errors', 1);

require './client.php';

$ip_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

$client = new Client($ip_addr);

$data = [];

if( isset($_GET['do']) ) {
  $action = $_GET['do'];

  switch($action) {
    case "topup":
      $client->pay();
      break;
    case "cancel":
      $client->cancel();
      break;
    default:
      exit('topup/cancel');
  }
}
else {
  $data = [
    "ip_addr" => $ip_addr,
    "mac_addr" => $client->mac_addr,
    "ping" => 1,
    "coins" => $client->coins,
    "mb_credit" => $client->mb_credit,
    "mb_used" => $client->mb_used,
    "coinslot_state" => $client->coinslot_state()
   ];
}

echo json_encode($data, JSON_PRETTY_PRINT);

