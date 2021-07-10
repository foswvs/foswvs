<?php
class Device {
  public $id;
  public $ip;
  public $mac;
  public $ping;

  function __construct($client_ip) {
    $this->ip = $client_ip;
    $this->mac = strtoupper($this->get_mac());
  }

  public function get_mac() {
    return exec("arp -an {$this->ip} | grep -o '..:..:..:..:..:..'");
  }

  public function get_active() {
    $cmd = shell_exec("arp -an|grep -oE '10\.0\.[0-9]{1,3}\.[0-9]{1,3}'");
    $ips = explode("\n", trim($cmd));
    return $ips;
  }
  public function ping() {
   return exec("ping -qA {$this->ip} -c1 -W1|grep -oP '/(\d+\.\d+)/'|grep -oP '\d+\.\d+'");
  }
}
