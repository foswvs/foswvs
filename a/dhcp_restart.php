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

if( system("sudo systemctl restart isc-dhcp-server.service") ) {
  echo "dhcp server restarted.";
}
