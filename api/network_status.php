<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

$ipt = new Iptables($IP);

if( !$ipt->connected() ) {
  http_response_code(403);
  exit;
}

exit; // let's skip, not necessary. remove this line if wanted

if( @file_get_contents('http://www.google.com/generate_204') === false ) {
  http_response_code(599);
  exit;
}
