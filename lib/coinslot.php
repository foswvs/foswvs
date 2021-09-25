<?php
/**
 * slot = 2 ; sensor = 17
 * usermod -aG gpio www-data
 */

class Coinslot {
  private $gpio = '/sys/class/gpio/';
  private $coin = 0;

  public function __construct() {
    if( !file_exists($this->gpio . 'gpio2') ) {
      $this->export_gpio2();
    }

    if( !file_exists($this->gpio . 'gpio17') ) {
      $this->export_gpio17();
    }
  }

  public function export_gpio2() {
    $fp = fopen($this->gpio . 'export', 'w');
    fwrite($fp, "2");
    fclose($fp);

    $this->cslot();
  }

  public function export_gpio17() {
    $fp = fopen($this->gpio . 'export', 'w');
    fwrite($fp, "17");
    fclose($fp);

    $this->sensor();
  }

  public function cslot() {
    $fp = fopen($this->gpio . 'gpio2/direction','w');
    fwrite($fp, 'in');
    fclose($fp);
  }

  public function sensor() {
    $fp = fopen($this->gpio . 'gpio17/direction','w');
    fwrite($fp, 'out');
    fclose($fp);
  }

  public function sensor_off() {
    $fp = fopen($this->gpio . 'gpio17/value','w');
    fwrite($fp, '0');
    fclose($fp);
  }

  public function sensor_on() {
    $fp = fopen($this->gpio . 'gpio17/value','w');
    fwrite($fp, '1');
    fclose($fp);
  }

  public function sensor_read() {
    $fp = fopen($this->gpio . 'gpio17/value','r');
    $val = stream_get_contents($fp);
    fclose($fp);

    return intval($val);
  }

  public function slot_read() {
    $fp = fopen($this->gpio . 'gpio2/value','r');
    $val = stream_get_contents($fp);
    fclose($fp);

    return intval($val);
  }

  public function coin_count() {
    $this->coin++;
  }

  public function get_coin() {
    return $this->coin;
  }
}
