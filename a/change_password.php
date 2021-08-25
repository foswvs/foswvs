<?php
if( !isset($_COOKIE['hash']) ) { http_response_code(403); exit; }

if( $_COOKIE['hash'] !== file_get_contents('/tmp/password') ) {
  http_response_code(401);
  exit;
}

$opt = array("options" => array("regexp" => "/.{5,25}/"));

if( $pwd = filter_input(INPUT_POST,'password', FILTER_VALIDATE_REGEXP, $opt) ) {
  file_put_contents('/tmp/password', hash('sha256', $pwd));
}

header('location: /a/login.php');
exit;
