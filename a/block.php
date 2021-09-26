<?php
if( !isset($_COOKIE['hash']) ) { http_response_code(403); exit; }

if( $_COOKIE['hash'] !== file_get_contents('../conf/password.sha256') ) {
  http_response_code(401);
  exit;
}

require '../lib/autoload.php';

$mac = filter_input(INPUT_GET,'mac', FILTER_VALIDATE_MAC);

if(!$mac) { http_response_code(403); exit; }

$db = new Database();

$db->set_mac($mac);

if( !$db->get_device_id() ) {
  http_response_code(403);
  exit;
}

[$mb_limit, $mb_used] = $db->get_data_usage();

$db->set_mb_used($mb_limit-$mb_used);

$db->update_mb_used();
