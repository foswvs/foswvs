<?php
class Session {
  public $id;

  public $db;
  public $device;
  public $coinslot;
  public $iptables;

  public $mb_used = 0;
  public $mb_limit = 0;
  public $mb_per_piso = 50;

  public $piso_count = 0;

  public $total_mb_used = 0;
  public $total_mb_limit = 0;

  public $wait = 30;
  public $timer = 0;
  public $start = 0;

  public $initr = true;

  public $connected;

  public $COINLOG = __DIR__ . "/coin.log";

  public function __construct($ip) {
    $this->device = new Device($ip);

    $this->recognized_device();

    $this->network_check();

    $this->coinslot_check();
  }

  public function recognized_device() {
    $this->db = new Database();

    $this->db->mac_addr = $this->device->mac;

    if( !$this->db->get_device_id() ) {
      $this->db->add_device();
    }

    $this->device->id = $this->db->devid;

    $this->id = $this->db->get_device_sid();
  }

  public function network_check() {
    $this->iptables = new Iptables($this->device->ip, $this->device->mac);

    $this->connected = $this->iptables->connected();

    $this->calc_usage();
  }

  public function calc_usage() {
    if( $this->connected ) {
      $this->mb_limit = $this->db->get_mb_limit();
      $this->mb_used = $this->db->get_mb_used();

      $this->total_mb_limit = $this->db->get_total_mb_limit();
      $this->total_mb_used = $this->db->get_total_mb_used();
    }
  }

  public function coinslot_check() {
    $this->coinslot = new Coinslot();

    $data = $this->readlog();

    if( $this->coinslot->relay_state() ) {

      if( !isset($data['mac']) ) $this->initr = false;

      if( isset($data['mac']) && $this->device->mac != $data['mac'] ) {
        $this->initr = false;
      }
    }

    if( isset($data['piso']) ) {
      $this->piso_count = $data['piso'];
    }

    if( isset($data['mb_limit']) ) {
      $this->mb_limit = $data['mb_limit'];
    }
  }

  public function topup() {
    $flog = fopen($this->COINLOG,'w');

    fseek($flog,0);
    fwrite($flog, json_encode(['mac' => $this->device->mac, 'piso' => 0,'mb_limit' => 0]));

    $this->start = time();

    $this->coinslot->activate();

    while($this->timer < $this->wait) {
      $this->coinslot->count();

      if( $this->coinslot->piso_count > 0 ) {
        $this->mb_limit = $this->coinslot->piso_count * $this->mb_per_piso;
        fseek($flog,0);
        fwrite($flog, json_encode(['mac' => $this->device->mac, 'piso' => $this->coinslot->piso_count,'mb_limit' => $this->mb_limit]));
      }

      if( !$this->coinslot->relay_state() ) {
        break;
      }

      $this->timer = time() - $this->start;
    }

    $this->coinslotOff();

    $this->mk_limit();

    fclose($flog);
  }

  public function mk_limit() {
    if( $this->mb_limit ) {
      $this->db->add_session();

      $this->db->mb_limit = $this->mb_limit;
      $this->db->set_mb_limit();

      $this->db->piso_count = $this->coinslot->piso_count;
      $this->db->set_piso_count();

      $this->db->set_device_sid();

      $this->iptables->add_client();
    }
  }

  public function coinslotOff() {
    if( $this->coinslot->relay_state() ) {
      $this->coinslot->deactivate();
    }
  }

  public function readlog() {
    $flog = fopen($this->COINLOG,'r');

    $data = fgets($flog);

    fclose($flog);

    $data = json_decode($data, true);

    if( $data['mac'] !== $this->device->mac ) {
      $data['piso'] = 0;
      $data['mb_limit'] = 0;
    }

    return $data;
  }
}
