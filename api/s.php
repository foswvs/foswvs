<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$db = new Database();

$db->set_ip($IP);

if( !$db->get_device_id_by_ip() ) {
  http_response_code(401);
  exit;
}

if( $_SERVER['REQUEST_METHOD'] == "GET" ) {

  $code = strtoupper(substr(uniqid(),8,5));

  $db->add_sharetx($code);

  exit($code);
}

if( $_SERVER['REQUEST_METHOD'] == "PUT" ) {
  $d = file_get_contents("php://input");

  $data = explode("|", $d);

  if( count($data) !== 2 )
    exit(http_response_code(403));

  list($code, $size) = $data;

  if( !filter_var($size, FILTER_VALIDATE_INT) ) {
    exit("Enter 1 to 999");
  }

  if( !$did = $db->get_sharetx_did(strtoupper($code)) ) {
    exit("Code Expired");
  }

  if( $db->get_did() === $did ) {
    exit("Not Allowed Using Own Code");
  }

  [$limit, $used] = $db->get_data_usage();
  $free = $limit - $used;

  if( $free < $size ) {
    exit("Insufficient Data");
  }

  $db->set_mb_used($size);
  $db->set_mb_limit($size);

  $db->update_mb_used();

  $db->set_did($did);
  $db->add_session();

  exit("Successfully Shared " . Helper::format_mb($size));
}

http_response_code(403);
