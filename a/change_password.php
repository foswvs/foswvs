<?php
if( !isset($_COOKIE['hash']) ) { http_response_code(403); exit; }

if( trim(file_get_contents('password.sha256')) !== $_COOKIE['hash'] ) {
  http_response_code(401);
  exit;
}

$opt = array("options" => array("regexp" => "/.{5,25}/"));

if( $pwd = filter_input(INPUT_POST,'password', FILTER_VALIDATE_REGEXP, $opt) ) {
  file_put_contents('password.sha256', hash('sha256', $pwd));
}

header('location: /a/login.php');
exit;
