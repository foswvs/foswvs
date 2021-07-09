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
    while( exec("sudo iptables -C FORWARD -s '{$this->ip}' -j ACCEPT; echo $?") ) {
      exec("sudo iptables -t nat -I PREROUTING -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -I FORWARD -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -I FORWARD -d {$this->ip} -j ACCEPT");
    }
  }

  public function rem_client() {
    while( exec("sudo iptables -C FORWARD -s '{$this->ip}' -j ACCEPT; echo $?") == 0 ) {
      exec("sudo iptables -t nat -D PREROUTING -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -D FORWARD -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -D FORWARD -d {$this->ip} -j ACCEPT");
    }
  }

  public function mb_used() {
    $data = shell_exec("sudo iptables -xvnL FORWARD | grep {$this->ip} | awk '{print $2}'");

    $bytes = array_sum(explode("\n", trim($data)));

    return round(($bytes/1000000));
  }

  function connected() {
    if( exec("sudo iptables -C FORWARD -s {$this->ip} -j ACCEPT; echo $?") ) {
      return false;
    }

    return true;
  }
  function init() {
    exec("sudo iptables -A FORWARD -s 10.0.0.0/20 -p tcp --dport 53 -j ACCEPT");
    exec("sudo iptables -A FORWARD -s 10.0.0.0/20 -p udp --dport 53 -j ACCEPT");

    exec("sudo iptables -A FORWARD -s 10.0.0.0/20 -p tcp --dport 80 -d 10.0.0.1 -j ACCEPT");
    exec("sudo iptables -A FORWARD -s 10.0.0.0/20 -j DROP");

    exec("sudo iptables -t nat -A PREROUTING -s 10.0.0.0/20 -p tcp --dport 80 -j DNAT --to-destination 10.0.0.1");
    exec("sudo iptables -t nat -A POSTROUTING -j MASQUERADE");
  }

  function flush() {
    exec("sudo iptables -F");
    exec("sudo iptables -t nat -F");
  }
}
