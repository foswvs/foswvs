<?php
require 'api/autoload.php';

$bytes = 0;

$bytes_cap = 0;
$bytes_used = 0;

if( isset($_GET['ip']) ) {
  $sess = new Session($_GET['ip']);

  if( $sess->db->get_device_id() ) {
    $bytes = $sess->iptables->mb_used();

    $sess->db->set_mb_used($bytes);

    $bytes_cap = $sess->db->get_total_mb_limit();
    $bytes_used = $sess->db->get_total_mb_used();

    if( $bytes_cap <= $bytes_used ) {
      $sess->iptables->rem_client();
    }
    else {
      $sess->iptables->add_client();
    }
  }
}

echo json_encode(['bytes_cap' => $bytes_cap, 'bytes_used' => $bytes_used]);
