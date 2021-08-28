<?php
$n = filter_input(INPUT_GET,'n', FILTER_VALIDATE_INT);

if( !$n ) { http_response_code(204); exit; }

require '../lib/helper.php';

echo Helper::amount_mb($n);
