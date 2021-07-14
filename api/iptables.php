<?php
/**
 * Note: Make sure you run this under sudoers
 * [username] ALL=NOPASSWD: /usr/sbin/iptables
 * via visudo
 */

class Iptables {
  public $ip;
  public $mac;

  function __construct($ip_addr, $mac_addr) {
    $this->ip = $ip_addr;
    $this->mac = $mac_addr;
  }

  public function add_client() {
    while( exec("sudo iptables -C FORWARD -d '{$this->ip}' -j ACCEPT; echo $?") ) {
      exec("sudo iptables -t nat -I PREROUTING -s {$this->ip} -j ACCEPT");
      //exec("sudo iptables -I FORWARD -m mac --mac-source {$this->mac} -j ACCEPT");
      exec("sudo iptables -I FORWARD -d {$this->ip} -j ACCEPT");
      exec("sudo iptables -I FORWARD -s {$this->ip} -j ACCEPT");
      sleep(1);
    }
  }

  public function rem_client() {
    while( exec("sudo iptables -C FORWARD -d '{$this->ip}' -j ACCEPT; echo $?") == 0 ) {
      exec("sudo iptables -t nat -D PREROUTING -s {$this->ip} -j ACCEPT");
      //exec("sudo iptables -D FORWARD -m mac --mac-source {$this->mac} -j ACCEPT");
      exec("sudo iptables -D FORWARD -d {$this->ip} -j ACCEPT");
      exec("sudo iptables -D FORWARD -s {$this->ip} -j ACCEPT");
      sleep(1);
    }
  }

  public function mb_used() {
    $data = shell_exec("sudo iptables -xvnL FORWARD | grep {$this->ip} | awk '{print $2}'");

    $bytes = array_sum(explode("\n", trim($data)));

    return round(($bytes/1000000));
  }

  public function connected() {
    $check1 = exec("sudo iptables -t nat -C PREROUTING -s {$this->ip} -j ACCEPT; echo $?");
    $check2 = exec("sudo iptables -C FORWARD -d {$this->ip} -j ACCEPT; echo $?");

    if( $check1 && $check1 ) {
      return false;
    }

    return true;
  }
}
