<?php
class Network {

  public function interfaces() {
    $cmd = shell_exec("ls /sys/class/net | grep -v lo");
    $net = explode("\n", trim($cmd));

    return $net;
  }

  public function dhcp_leases() {
    $cmd = shell_exec("dhcp-lease-list --parsable |awk '{ print $2 \"||\" $4 \"||\" $6 \"||\" $8 \" @ \" $9 \"||\" $11 \" @ \" $12}'");
    $dev = array_map(function($s) { return array_combine(['mac','ip','host','begin','expire'], explode("||", $s) ); },  explode("\n",trim($cmd)) );

    return $dev;
  }

  public function arp_list() {
    $cmd = shell_exec("arp -an|grep -oE '10\.0\.[0-9]{1,3}\.[0-9]{1,3}'");
    $ips = explode("\n", trim($cmd));

    return $ips;
  }
}
