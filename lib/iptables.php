<?php
/**
 * Note: Make sure you run this under sudoers
 * [username] ALL=NOPASSWD: /usr/sbin/iptables
 * via visudo
 */

class Iptables {
  public $ip;

  function __construct($ip_addr) {
    $this->ip = $ip_addr;
  }

  public function add_client() {
    while( shell_exec("sudo iptables -nL FORWARD | grep -w '{$this->ip}'") == NULL ) {
      exec("sudo iptables -t nat -I PREROUTING -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -A FORWARD -d {$this->ip} -j ACCEPT");
      exec("sudo iptables -A FORWARD -s {$this->ip} -j ACCEPT");
      sleep(1);
    }
  }

  public function rem_client() {
    while( shell_exec("sudo iptables -nL FORWARD | grep -w '{$this->ip}'") ) {
      exec("sudo iptables -t nat -D PREROUTING -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -D FORWARD -d {$this->ip} -j ACCEPT");
      exec("sudo iptables -D FORWARD -s {$this->ip} -j ACCEPT");
      sleep(1);
    }
  }

  public function connected() {
    if( shell_exec("sudo iptables -nL FORWARD | grep -w '{$this->ip}'") == NULL ) {
      return false;
    }

    return true;
  }
}
