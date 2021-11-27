<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$db = new Database();

$db->set_ip($IP);

if( !$db->get_device_id_by_ip() ) {
  http_response_code(401);
  exit;
}

if( $_SERVER['REQUEST_METHOD'] == "POST" ) {
  $d = file_get_contents("php://input");

  if( filter_var($d, FILTER_VALIDATE_INT) ) {
    $code = strtoupper(substr(uniqid(),8,5));

    $db->add_sharetx($code, $d);

    exit($code);
  }
}

if( $_SERVER['REQUEST_METHOD'] == "PUT" ) {
  $d = file_get_contents("php://input");

  if( strlen($d) === 5 ) {
    [$dev, $mbs] = $db->get_sharetx($d);

    if( is_null($dev) ) {
      exit("code expire");
    }

    $db->rem_sharetx($d);

    if( $db->get_total_mb_used() < $mbs ) {
      echo "insufficient data";

      exit(http_response_code(403));
    }

    $db->set_mb_used($mbs);
    $db->update_mb_used();

    $db->set_mb_limit($mbs);

    $db->set_did($dev);
    $db->add_session();

    exit("successfully shared " . Helper::format_mb($mbs));
  }

  if( $d === 'NaN' ) {
    echo "input mb size";
  }
}

exit(http_response_code(403));
