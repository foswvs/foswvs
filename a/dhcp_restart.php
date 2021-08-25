<?php
if( !isset($_COOKIE['hash']) ) { http_response_code(403); exit; }

if( $_COOKIE['hash'] !== file_get_contents('/tmp/password') ) {
  http_response_code(401);
  exit;
}

if( system("sudo systemctl restart isc-dhcp-server.service") ) {
  echo "dhcp server restarted.";
}
