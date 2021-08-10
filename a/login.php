<?php
$hash = trim(file_get_contents('password.sha256'));
$login = false;

if( hash('sha256', filter_input(INPUT_POST,'password')) === $hash ) {
  setcookie('hash', $hash, time() + 86400, '/a/'); $login = true;
}

if( isset($_COOKIE['hash']) && $_COOKIE['hash'] === $hash ) $login = true;

header('location: /a/' . ($login ? 'active_devices.html' : 'login.html'));
exit;
