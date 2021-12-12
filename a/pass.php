<?php
/* setup admin password while currently not in use */
$password = '../conf/password.sha256';

if( $_SERVER['REQUEST_METHOD'] === 'HEAD' ) {
  if( !file_exists($password) ) {
    $temp = hash('sha256', time());

    file_put_contents($password, $temp);

    setcookie('hash', $temp, time() + 3600, '/a/');

    exit(http_response_code(201));
  }

  exit;
}

$hash = file_get_contents($password);

if( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
  if( !isset($_COOKIE['hash']) ) {
    exit(http_response_code(401));
  }

  if( $_COOKIE['hash'] !== $hash ) {
    exit(http_response_code(401));
  }

  exit;
}

if( $_SERVER['REQUEST_METHOD'] === 'PUT' ) {
  $d = file_get_contents('php://input');

  if( empty($d) ) {
    exit(http_response_code(400));
  }

  $d = base64_decode($d);

  if( empty($d) ) {
    exit(http_response_code(400));
  }

  if( hash('sha256', $d) !== $hash ) {
    exit(http_response_code(401));
  }

  setcookie('hash', $hash, time() + 36000, '/a/');

  exit;
}

if( $_SERVER['REQUEST_METHOD'] === 'PATCH' ) {
  if( !isset($_COOKIE['hash']) ) {
    exit(http_response_code(401));
  }

  if( $_COOKIE['hash'] !== $hash ) {
    exit(http_response_code(401));
  }

  $d = file_get_contents('php://input');

  if( empty($d) ) {
    exit(http_response_code(400));
  }

  $d = base64_decode($d);

  if( empty($d) ) {
    exit(http_response_code(400));
  }

  if( strlen($d) < 3 ) {
    exit(http_response_code(401));
  }

  $d = hash('sha256', $d);

  if( $d === $hash ) {
    exit;
  }

  file_put_contents($password, $d);

  exit;
}

if( $_SERVER['REQUEST_METHOD'] === 'DELETE' ) {
  setcookie('hash','',-1,'/a/');
}
