<?php

setcookie('hash',md5(1),time() + 60,"/");

var_dump($_COOKIE);
