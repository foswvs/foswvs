<?php
class Network {

  public function interfaces() {
    $cmd = shell_exec("ls /sys/class/net | grep -v lo");
    $net = explode("\n", trim($cmd));

    return $net;
  }

  public function dhcp_leases() {
    $cmd = shell_exec("dhcp-lease-list --parsable |awk '{ print $2 \"||\" $4 \"||\" $6 \"||\" $8 \" @ \" $9 \"||\" $11 \" @ \" $12}'");

    if(!$cmd) return [];

    $dev = array_map(function($a) { return array_combine(['mac','ip','host','begin','expire'], explode("||", $a) ); },  explode("\n",trim($cmd)) );
    $dev = array_map(function($a){ foreach($a as $a0=>$a1){ if($a0 == 'mac'){ $a[$a0] = strtoupper($a1); } } return $a; }, $dev);

    return $dev;
  }

  public function arp_list() {
    $cmd = shell_exec("arp -an|grep -oE '10\.0\.[0-9]{1,3}\.[0-9]{1,3}'");
    $ips = explode("\n", trim($cmd));

    return $ips;
  }

  public static function device_mac($ip) {
    $cmd = exec("arp -an {$ip} | grep -o '..:..:..:..:..:..'");

    return strtoupper($cmd);
  }
}
