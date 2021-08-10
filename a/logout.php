<?php
setcookie('hash', '', -1, '/a/');
header('location: /a/index.html');
exit;
