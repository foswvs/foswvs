<?php
if( !isset($_COOKIE['hash']) ) { http_response_code(403); exit; }

if( trim(file_get_contents('password.sha256')) !== $_COOKIE['hash'] ) {
  http_response_code(401);
  exit;
}

require '../lib/autoload.php';

$mac = filter_input(INPUT_GET,'mac', FILTER_VALIDATE_MAC);

if(!$mac) { http_response_code(403); exit; }

$db = new Database();

$db->mac_addr = $mac;

if( !$db->get_device_id() ) {
  http_response_code(403);
  exit;
}

[$mb_limit, $mb_used] = $db->get_data_usage();

$db->mb_used = $mb_limit - $mb_used;

$db->set_mb_used();
