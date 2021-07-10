<?php
require_once '../api/autoload.php';

$ip_addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);

if( filter_input(INPUT_GET, 'do') == 'active_devices') {
  $device = new Device($ip_addr);

  echo json_encode($device->get_active());
}

