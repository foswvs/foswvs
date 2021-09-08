<?php
function foswvsClasses($filename) {
  require_once __DIR__ . "/" . strtolower($filename) . ".php";
}

spl_autoload_register("foswvsClasses");