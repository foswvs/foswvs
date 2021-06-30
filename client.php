<?php
require './coinslot.php';

class Client extends Coinslot {

  public $ip_addr;
  public $mac_addr;
  public $mb_used = 0;
  public $mb_credit = 0;
  public $mb_per_coin = 50;

  function __construct($client_ip) {
    $this->ip_addr = $client_ip;
    $this->mac_addr = $this->get_mac();
    $this->check_credits();
  }

  public function get_mac() {
    return exec("arp {$this->ip_addr} | grep -o '..:..:..:..:..:..'");
  }

  public function mac_str() {
    return strtoupper(str_replace(":", "", $this->mac_addr));
  }

  public function check_credits(){
    $log = "./log/" . $this->mac_str();

    if( !file_exists($log) ) {
      file_put_contents($log, ["mac" => $this->mac_addr, "ip" => $this->ip_addr, "mb_credit" => 0, "mb_used" => 0] );
    }

    $log = file_get_contents($log);
    $log = json_decode($log, true);

    $this->mb_credit = $log['mb_credit'];
  }

  public function push_data() {
    $data = ["ip" => $this->ip_addr, "mac" => $this->mac_addr, "mb_credit" => $this->mb_credit ];

    file_put_contents("./log/" . $this->mac_str(), json_encode($data) );
  }

  public function pull_data() {
    $data = file_get_contents("./log/" . $this->mac_str() );
    $data = json_decode($data, true);

    return $data;
  }

  public function timer() {
    return $this->timer;
  }

  public function pay() {
    $this->counter($this->mac_addr);
  }

  public function coinslot_state() {
    return $this->ready();
  }

  public function cancel() {
    $this->deactivate();
  }

  public function ping() {
   return exec("ping 1.1.1.1 -c1 -W2|grep -oP '/(\d+\.\d+)/'|grep -oP '\d+\.\d+'");
  }
}
