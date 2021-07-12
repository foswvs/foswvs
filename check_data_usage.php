<?php
require 'api/autoload.php';

$mb_used = 0;

$total_mb_used = 0;
$total_mb_limit = 0;

$mac = NULL;

if( $IP = filter_input(INPUT_GET, 'ip', FILTER_VALIDATE_IP) ) {
  $sess = new Session($IP);

  $mac = $sess->device->mac;

  if( empty($mac) ) exit;

  if( $sess->db->get_device_id() ) {
    $mb_used = $sess->iptables->mb_used();

    $sess->db->set_mb_used($mb_used);

    $total_mb_limit = $sess->db->get_total_mb_limit();
    $total_mb_used = $sess->db->get_total_mb_used();

    if( $total_mb_limit <= $total_mb_used ) {
      $sess->iptables->rem_client();
    }
    else {
      $sess->iptables->add_client();
    }
  }
}

if( isset($debug) ) {
  echo json_encode(['total_mb_limit' => $total_mb_limit, 'total_mb_used' => $total_mb_used, 'mac' => $mac]);
}
