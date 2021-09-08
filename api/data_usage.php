<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
$db = new Database();

$db->set_ip($IP);

if( !$db->get_device_id_by_ip() ) {
  http_response_code(401);
  exit;
}

$MAC = $db->get_device_mac();

if( isset($_COOKIE['hwid']) ) {
  $hwid = base64_decode(base64_decode($_COOKIE['hwid']));

  if( $hwid !== $MAC ) {
    $db->update_session_from_random_mac($hwid);
    setcookie('hwid', base64_encode(base64_encode($MAC)), time() + 604800, '/');
  }
}

list($mb_limit,$mb_used) = $db->get_data_usage();

if( !isset($_COOKIE['hwid']) && $mb_limit > $mb_used ) {
  setcookie('hwid', base64_encode(base64_encode($MAC)), time() + 604800, '/');
}

echo json_encode(['ip' => $IP, 'mac' => $MAC, 'mb_limit' => $mb_limit, 'mb_used' => $mb_used]);