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

  public $coins = 0;
  public $timer = 0;
  public $start = 0;
  public $wait = 30;

  function __construct() {
    if(!`which gpio`) exit('gpio must be installed.' . PHP_EOL);

    shell_exec("gpio mode " . self::COIN . " in");
    shell_exec("gpio mode " . self::RELAY . " out");
  }

  public function activate() {
    shell_exec("gpio write " . self::RELAY . " 1");
  }

  public function counter($mac) {
    $this->start = time();

    $this->activate();

    while( $this->timer < $this->wait ) {
      if( shell_exec("gpio read " . self::COIN) == 1 ) {
        $this->coins++;
        $data = json_encode(["mac" => $mac, "coins" => $this->coins]);
        file_put_contents("./log/coinslot.json", $data );
        usleep(17000); // set according to accuracy
      }

      $this->timer = time() - $this->start;
    }
    $this->deactivate();
  }

  public function deactivate() {
    shell_exec("gpio write " . self::RELAY . " 0");
  }

  public function ready() {
    if( shell_exec("gpio read " . self::RELAY) == 0 ) {
      return false;
    }
    return true;
  }
}
