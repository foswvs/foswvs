<?php
ini_set('display_errors', 1);

require 'coinslot.php';
require 'database.php';

class Device extends Coinslot {
  private $db;

  public $ip_addr;
  public $mac_addr;
  public $device_id;

  public $mb_used = 0;
  public $mb_credit = 0;
  public $mb_per_piso = 50;

  public $wait = 0;
  public $timer = 0;
  public $start = 0;

  function __construct($client_ip) {
    $this->ip_addr = $client_ip;
    $this->mac_addr = $this->get_mac();

    $this->db = new Database();

    if( !$this->db->get_device_id($this->mac_addr) ) {
      $this->db->add_device($this->mac_addr, $this->ip_addr);
    }

    $this->device_id = $this->db->get_device_id($this->mac_addr);
  }

  public function get_mac() {
    return exec("arp {$this->ip_addr} | grep -o '..:..:..:..:..:..'");
  }

  public function mac_str() {
    return strtoupper(str_replace(":", "", $this->mac_addr));
  }

  public function topup() {
    $this->start = time();

    $this->activate();

    while($this->timer < $this->wait) {
      $this->count();

      if( !$this->state() ) {
        break;
      }

      if( $this->piso_count > 0 ) {
        $this->mb_credit = $this->piso_count * $this->mb_per_piso;
      }

      $this->timer = time() - $this->start;
    }

    $this->db->add_session($this->device_id, $this->piso_count, $this->mb_credit);

    $this->deactivate();
  }

  public function ping() {
   return exec("ping 1.1.1.1 -c1 -W2|grep -oP '/(\d+\.\d+)/'|grep -oP '\d+\.\d+'");
  }
}
