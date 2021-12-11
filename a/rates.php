<?php
$password = '../conf/password.sha256';

if( !isset($_COOKIE['hash']) ) { http_response_code(403); exit; }

if( $_COOKIE['hash'] !== file_get_contents($password) ) {
  http_response_code(401);
  exit;
}

$rfile = __DIR__ . '/../conf/rates.json';

if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
  $rates = [];

  foreach($_POST['rates'] as $k=>$v) {
    $key = intval($k);
    $val = intval($v);

    if( $key > 0 && $val > 0 ) {
      $rates[$key] = $val;
    }
  }

  if( count($rates) >= 3 ) {
    file_put_contents($rfile, json_encode($rates));
  }

  header('location: /a/rates.html'); exit;
}

if( $_SERVER['REQUEST_METHOD'] === 'PUT' ) {
  $a = json_decode(file_get_contents('php://input'), true);

  if( !$a ) exit(http_response_code(403));

  if( !is_array($a) ) exit(http_response_code(403));

  if( count($a) < 1 ) exit(http_response_code(403));

  $u = array_unique($a);
  $t = [];
  $x = [];

  foreach($u as $r) {
    [$amt, $mbs] = explode(':', $r);

    if( !in_array($amt, $t) ) {
      $a = intval($amt);
      $b = intval($mbs);

      if( $a > 0 && $b > 0 ) {
        array_push($t, $amt);
        $x[$a] = $b;
      }
    }
  }

  if( empty($x) ) exit(http_response_code(403));

  file_put_contents($rfile, json_encode($x));

  exit("new rates saved");
}

if( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
  echo file_get_contents($rfile);
}
