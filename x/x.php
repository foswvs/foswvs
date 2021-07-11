<?php
require_once '../api/autoload.php';

if( filter_input(INPUT_GET, 'network') == 'dhcp_leases') {
  $network = new Network();


  echo json_encode($network->dhcp_leases(), JSON_PRETTY_PRINT);
}

if( filter_input(INPUT_GET, 'device') == 'add_session' ) {
  if( $IP = filter_input(INPUT_GET, 'ip') ) {
    $sess = new Session($IP);

    $sess->mb_limit = 1024;
    $sess->mk_limit();

    echo json_encode(['mb_limit' => $sess->mb_limit]);
  }
}
