<?php
require 'autoload.php';

$total_mb_used = 0;
$total_mb_limit = 0;

if( $IP = filter_input(INPUT_GET, 'ip', FILTER_VALIDATE_IP) ) {
  $sess = new Session($IP);

  if( empty($sess->device->mac) ) exit;

  if( $sess->db->get_device_id() ) {
    $sess->db->mb_used = $sess->iptables->mb_used();

    $sess->db->set_mb_used();

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
