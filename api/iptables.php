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
    while( shell_exec("sudo iptables -nL FORWARD | grep '{$this->ip}'") == NULL ) {
      exec("sudo iptables -t nat -I PREROUTING -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -I FORWARD -d {$this->ip} -j ACCEPT");
      exec("sudo iptables -I FORWARD -s {$this->ip} -j ACCEPT");
      usleep(1e5);
    }
  }

  public function rem_client() {
    while( shell_exec("sudo iptables -nL FORWARD | grep '{$this->ip}'") ) {
      exec("sudo iptables -t nat -D PREROUTING -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -D FORWARD -d {$this->ip} -j ACCEPT");
      exec("sudo iptables -D FORWARD -s {$this->ip} -j ACCEPT");
      usleep(1e5);
    }
  }

  public function zero_byte() {
    $cmd = shell_exec("sudo iptables -nL FORWARD --line|grep '{$this->ip}'|awk '{print $1}'");

    $num = explode("\n", trim($cmd));

    foreach($num as $n) {
      exec("sudo iptables -Z FORWARD $n");
    }
  }

  public function mb_used() {
    $data = shell_exec("sudo iptables -xvnL FORWARD | grep {$this->ip} | awk '{print $2}'");

    $bytes = array_sum(explode("\n", trim($data)));

    return round($bytes/1e6);
  }

  public function connected() {
    if( shell_exec("sudo iptables -nL FORWARD | grep '{$this->ip}'") == NULL ) {
      return false;
    }

    return true;
  }
}
