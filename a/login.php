<?php
if( !file_exists('/tmp/password') ) {
  $temp = hash('sha256', time());

  setcookie('hash', $temp, time() + 3600, '/a/');
  file_put_contents('/tmp/password', $temp);

  header('location: /a/change_password.html');
  exit;
}

$hash = trim(file_get_contents('/tmp/password'));
$login = false;

if( hash('sha256', filter_input(INPUT_POST,'password')) === $hash ) {
  setcookie('hash', $hash, time() + 86400, '/a/'); $login = true;
}

if( isset($_COOKIE['hash']) && $_COOKIE['hash'] === $hash ) $login = true;

header('location: /a/' . ($login ? 'active_devices.html' : 'login.html'));
exit;
