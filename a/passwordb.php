<?php
if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) exit(http_response_code(403));

if( !isset($_COOKIE['hash']) ) exit(http_response_code(403));

if( $_COOKIE['hash'] !== file_get_contents('password.sha256') ) {
  http_response_code(401);
  exit;
}

$opt = array("options" => array("regexp" => "/.{5,25}/"));
$pwd = base64_decode(base64_decode(file_get_contents('php://input')));

if( filter_var($pwd, FILTER_VALIDATE_REGEXP, $opt) ) {
  file_put_contents('password.sha256', hash('sha256', $pwd));
  http_response_code(202);
  exit;
}
