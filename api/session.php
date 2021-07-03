<?php
class Session {
  public $id;

  public $db;
  public $device;
  public $coinslot;

  public $mb_used = 0;
  public $mb_credit = 0;
  public $mb_per_piso = 50;

  public $piso_count = 0;

  public $wait = 20;
  public $timer = 0;
  public $start = 0;

  function __construct($ip) {
    $this->db = new Database();

    $this->device = new Device($ip);

    $this->coinslot = new Coinslot();

    $this->db->ip_addr = $this->device->ip;
    $this->db->mac_addr = $this->device->mac;

    if( !$this->db->get_device_id() ) {
      $this->db->add_device();
    }

    $this->device->id = $this->db->devid;

    $this->mb_credit = $this->db->get_mb_credit();
    $this->mb_used = $this->db->get_mb_used();

    $this->id = $this->db->get_device_session();

    $this->piso_count = $this->db->get_piso_count();
  }

  function topup() {
    $this->start = time();

    $this->coinslot->activate();

    $this->db->add_session();

    while($this->timer < $this->wait) {
      $this->coinslot->count();

      if( !$this->coinslot->relay_state() ) {
        break;
      }

      if( $this->coinslot->piso_count > 0 ) {
        $this->mb_credit = $this->coinslot->piso_count * $this->mb_per_piso;
        $this->db->set_piso_count();
      }

      $this->timer = time() - $this->start;
    }

    $this->db->set_mb_credit($this->mb_credit);
    $this->db->set_device_session();

    $this->coinslot->deactivate();
  }
}
