<?php
session_start();

if( !isset($_SESSION['hash']) ) {
  http_response_code(401);
  exit;
}

if( file_get_contents('password.sha256') !== $_SESSION['hash'] ) {
  http_response_code(401);
  exit;
}

require '../lib/autoload.php';

$coinslot = new Coinslot();

$coinslot->sensor_off();

if( !$coinslot->sensor_read() ) {
  echo "coinslot switch off";
}
