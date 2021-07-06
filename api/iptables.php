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

  function add_client() {
    while( shell_exec("sudo iptables -nL FORWARD | grep '{$this->mac}'") == NULL) {
      exec("sudo iptables -t nat -I PREROUTING -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -I FORWARD -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -I FORWARD -m mac --mac-source {$this->mac} -j ACCEPT");
    }
  }

  function rm_client() {
    while( shell_exec("sudo iptables -nL FORWARD | grep '{$this->mac}'") ) {
      exec("sudo iptables -D FORWARD -s {$this->ip} -j ACCEPT");
      exec("sudo iptables -D FORWARD -m mac --mac-source {$this->mac} -j ACCEPT");
    }
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
