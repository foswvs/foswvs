<?php
class Session {
  public $id;

  public $db;
  public $device;
  public $coinslot;
  public $iptables;

  public $mb_used = 0;
  public $mb_limit = 0;
  public $mb_per_piso = 100;

  public $total_mb_used = 0;
  public $total_mb_limit = 0;

  public $wait = 30;
  public $timer = 0;
  public $start = 0;

  public $initr = true;

  public $COINLOG = __DIR__ . "/coin.log";

  public function __construct($ip) {
    $this->device = new Device($ip);

    $this->coinslot = new Coinslot();

    $this->iptables = new Iptables($ip, $this->device->mac);

    $this->device_check();

    $this->coinslot_check();

    $this->get_mb_usage();
  }

  public function device_check() {
    $this->db = new Database();

    $this->db->ip_addr = $this->device->ip;
    $this->db->mac_addr = $this->device->mac;

    if( $this->db->get_device_id() == 0 ) {
      $this->db->add_device();
    }

    $this->id = $this->db->get_device_sid();
  }

  public function coinslot_check() {
    if( $this->coinslot->sensor_read() ) {
      $file = file_get_contents($this->COINLOG);

      $data = json_decode($file, true);

      if( $this->device->mac == $data['mac'] ) {
        $this->mb_limit = $data['mb_limit'];
      }
      else {
        $this->initr = false;
      }
    }
  }

  public function get_mb_usage() {
    $this->total_mb_limit = $this->db->get_total_mb_limit();
    $this->total_mb_used = $this->db->get_total_mb_used();
  }

  public function topup() {
    $flog = fopen($this->COINLOG,'w');

    fseek($flog,0);
    fwrite($flog, json_encode(['mac' => $this->device->mac, 'mb_limit' => 0, 'timer' => $this->wait]) );

    $this->start = time();

    $this->coinslot->sensor_on();

    while($this->timer < $this->wait) {

      $this->coinslot->coin_count();

      if( $this->coinslot->get_coin() > 0 ) {
        $this->mb_limit = $this->calc_data($this->coinslot->get_coin());
      }

      fseek($flog,0);
      fwrite($flog, json_encode(['mac' => $this->device->mac, 'mb_limit' => $this->mb_limit, 'timer' => [$this->wait, $this->timer]]) );

      if( $this->coinslot->sensor_read() ) {
        break;
      }

      $this->timer = time() - $this->start;
    }

    fclose($flog);

    $this->coinslot->sensor_off();

    $this->mk_limit();

  }

  public function calc_data($amt) {
    $data = 0;

    if( $amt >= 10 ) {
      $base = floor($amt / 10);
      $data = $base * 1500;
      $tens = $base * 10;
      $amt = $amt - $tens;
    }

    if( $amt >= 5 ) {
      $base = floor($amt / 5);
      $data = $base * 500 + $data;
      $ones = $base * 5;
      $amt = $amt - $ones;
    }

    return ($amt * 50) + $data;
  }

  public function mk_limit() {
    if( $this->mb_limit ) {
      $this->db->mb_limit = $this->mb_limit;
      $this->db->piso_count = $this->coinslot->get_coin();

      $this->db->add_session();
      $this->db->set_mb_limit();

      $this->db->set_piso_count();
      $this->db->set_device_sid();

      if( $this->iptables->connected() ) {
        $this->iptables->zero_byte();
      }
      else {
        $this->iptables->add_client();
      }
    }
  }

  public function get_timer() {
    $file = file_get_contents($this->COINLOG);

    $data = json_decode($file, true);

    return $data['timer'];
  }
}
