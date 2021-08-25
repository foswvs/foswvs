<?php
$a = filter_input(INPUT_GET,'a', FILTER_VALIDATE_INT);

if(!$a) exit;

require '../lib/helper.php';

$h = new Helper();

echo $h->amt_to_mb($a);
