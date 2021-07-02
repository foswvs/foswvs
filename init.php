<?php
require 'database.php';

class Initialize extends Database {
  function __construct() {
    parent::create_table();
  }
}

new Initialize();
