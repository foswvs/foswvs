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

  public $total_mb_used = 0;
  public $total_mb_credit = 0;

  public $wait = 20;
  public $timer = 0;
  public $start = 0;

  private $COINLOG = __DIR__ . "/coin.log";

  function __construct($ip) {
    $this->db = new Database();

    $this->device = new Device($ip);

    $this->coinslot = new Coinslot();

    $this->db->mac_addr = $this->device->mac;

    if( !$this->db->get_device_id() ) {
      $this->db->add_device();
    }

    $this->device->id = $this->db->devid;

    $this->id = $this->db->get_device_session();

    $this->mb_credit = $this->db->get_mb_credit();
    $this->mb_used = $this->db->get_mb_used();

    $this->total_mb_credit = $this->db->get_total_mb_credit();
    $this->total_mb_used = $this->db->get_total_mb_used();

    $data = $this->readlog();

    $this->mb_credit = $data['mb_credit'];
    $this->piso_count = $data['piso'];
  }

  function topup() {
    $flog = fopen($this->COINLOG,'w');

    $this->start = time();

    $this->coinslot->activate();

    $this->db->add_session();

    while($this->timer < $this->wait) {
      $this->coinslot->count();

      if( $this->coinslot->piso_count > 0 ) {
        $this->mb_credit = $this->coinslot->piso_count * $this->mb_per_piso;
        fseek($flog,0);
        fwrite($flog, json_encode(['mac' => $this->device->mac, 'piso' => $this->coinslot->piso_count,'mb_credit' => $this->mb_credit]));
      }

      if( !$this->coinslot->relay_state() ) {
        break;
      }

      $this->timer = time() - $this->start;
    }

    $this->db->set_mb_credit($this->mb_credit);
    $this->db->set_device_session();

    $this->coinslot->deactivate();

    fclose($flog);
  }

  function readlog() {
    $flog = fopen($this->COINLOG,'r');

    $data = fgets($flog);

    fclose($flog);

    $data = json_decode($data, true);

    if( $data['mac'] !== $this->device->mac ) {
      $data['piso'] = 0;
      $data['mb_credit'] = 0;
    }

    return $data;
  }
}
