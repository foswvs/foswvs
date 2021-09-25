<?php
/* setup admin password while currently not in use */
$login = false; $password = '../conf/password.sha256';

if( !file_exists($password) ) {
  $temp = hash('sha256', time());

  setcookie('hash', $temp, time() + 3600, '/a/');
  file_put_contents($password, $temp);

  header('location: /a/password.html');
  exit;
}

$hash = file_get_contents($password);

if( hash('sha256', filter_input(INPUT_POST,'password')) === $hash ) {
  setcookie('hash', $hash, time() + 36000, '/a/'); $login = true;
}

if( isset($_COOKIE['hash']) && $_COOKIE['hash'] === $hash ) $login = true;

header('location: /a/' . ($login ? 'view.html' : 'login.html'));
exit;
