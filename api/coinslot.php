<?php
/**
 * Note: WiringPi must be installed to perform this operation.
 * wget https://project-downloads.drogon.net/wiringpi-latest.deb
 * sudo dpkg -i wiringpi-latest.deb
 *
 * @author foswvs
 */

class Coinslot {
  /**
   * Configure according to your setup
   * see `gpio readall` for more information.
   */
  const COIN = 8;
  const RELAY = 0;

  public $piso_count = 0;
  public $slot_state;

  function __construct() {
    if(!`which gpio`) exit('gpio must be installed.' . PHP_EOL);

    shell_exec("gpio mode " . self::COIN . " in");
    shell_exec("gpio mode " . self::RELAY . " out");

    $this->slot_state = $this->relay_state();
  }

  public function activate() {
    shell_exec("gpio write " . self::RELAY . " 1");
  }

  public function count() {
    if( shell_exec("gpio read " . self::COIN) == 1 ) {
      $this->piso_count++; usleep(17000);
    }
  }

  public function deactivate() {
    shell_exec("gpio write " . self::RELAY . " 0");
  }

  public function relay_state() {
    if( shell_exec("gpio read " . self::RELAY) == 0 ) {
      return false;
    }
    return true;
  }

  public function init() {
    self::__construct();
  }
}
