<?php
/**
 * Note: Make sure you run this under sudoers
 */

class Iptables {
  public $ip;
  public $mac;

  function __construct($ip_addr, $mac_addr) {
    $this->ip = $ip_addr;
    $this->mac = $mac_addr;
  }

  function add_client() {
    system("sudo iptables -t nat -I PREROUTING 1 -s {$this->ip} -j ACCEPT");
    system("sudo iptables -I FORWARD -s {$this->ip} -j ACCEPT");
    system("sudo iptables -I FORWARD -m mac --mac-source {$this->mac} -j ACCEPT");
  }

  function rm_client() {
    system("sudo iptables -t nat -D PREROUTING -s {$this->ip} -j ACCEPT");
    system("sudo iptables -D FORWARD -s {$this->ip} -j ACCEPT");
    system("sudo iptables -D FORWARD -m mac --mac-source {$this->mac} -j ACCEPT");
  }

  function init() {
    system("sudo iptables -A FORWARD -s 10.0.0.0/20 -p tcp --dport 53 -j ACCEPT");
    system("sudo iptables -A FORWARD -s 10.0.0.0/20 -p udp --dport 53 -j ACCEPT");

    system("sudo iptables -A FORWARD -s 10.0.0.0/20 -p tcp --dport 80 -d 10.0.0.1 -j ACCEPT");
    system("sudo iptables -A FORWARD -s 10.0.0.0/20 -j DROP");

    system("sudo iptables -t nat -A PREROUTING -s 10.0.0.0/20 -p tcp --dport 80 -j DNAT --to-destination 10.0.0.1");
    system("sudo iptables -t nat -A POSTROUTING -j MASQUERADE");
  }

  function flush() {
    system("sudo iptables -F");
    system("sudo iptables -t nat -F");
  }
}
