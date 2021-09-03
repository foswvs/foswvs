<?php
/* setup admin password while currently not in use */
if( !file_exists('/tmp/coinslot') ) {
  $temp = hash('sha256', time());

  setcookie('hash', $temp, time() + 3600, '/a/');
  file_put_contents('password.sha256', $temp);

  header('location: /a/change_password.html');
  exit;
}

$hash = trim(file_get_contents('password.sha256'));
$login = false;

if( hash('sha256', filter_input(INPUT_POST,'password')) === $hash ) {
  setcookie('hash', $hash, time() + 36000, '/a/'); $login = true;
}

if( isset($_COOKIE['hash']) && $_COOKIE['hash'] === $hash ) $login = true;

header('location: /a/' . ($login ? 'view.html' : 'login.html'));
exit;
