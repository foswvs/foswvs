<?php
ini_set('display_errors', 1);

require __DIR__ . '/api/autoload.php';

class Initialize extends Database {
  function __construct() {
    parent::__construct();

    echo "Database created!" . PHP_EOL;
  }
}

new Initialize();

