<?php
if( !isset($_COOKIE['hash']) ) { http_response_code(403); exit; }

if( $_COOKIE['hash'] !== file_get_contents('/tmp/password') ) {
  http_response_code(401);
  exit;
}

require '../lib/autoload.php';

$coinslot = new Coinslot();

$coinslot->sensor_off();

if( !$coinslot->sensor_read() ) {
  echo "coinslot switch off";
}
