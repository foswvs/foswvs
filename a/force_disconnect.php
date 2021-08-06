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

$ip_addr = filter_input(INPUT_GET, 'ip', FILTER_VALIDATE_IP);

while( shell_exec("sudo iptables -nL FORWARD | grep '{$ip_addr}'") ) {
  exec("sudo iptables -t nat -D PREROUTING -s {$ip_addr} -j ACCEPT");
  exec("sudo iptables -D FORWARD -d {$ip_addr} -j ACCEPT");
  exec("sudo iptables -D FORWARD -s {$ip_addr} -j ACCEPT");
  usleep(1e5);
}

