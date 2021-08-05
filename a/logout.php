<?php
session_start();
session_destroy();
header('location: /a/index.html');
exit;
