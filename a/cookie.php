<?php
if( !isset($_COOKIE['hash']) ) { http_response_code(403); exit; }

if( trim(file_get_contents('password.sha256')) !== $_COOKIE['hash'] ) {
  http_response_code(401);
  exit;
}
