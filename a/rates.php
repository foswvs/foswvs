<?php
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

echo file_get_contents($rfile);
