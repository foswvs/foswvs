#!/usr/bin/php
<?php
require __DIR__ . '/autoload.php';

$db = new Database();

$cmd = shell_exec("sudo iptables -nvxL FORWARD --line-numbers|awk 'NR>2{print $1 \"|\" $3 \"|\" $9 \"|\" $10}'");

if( empty($cmd) ) exit;

$data = explode("\n", trim($cmd));

foreach($data as $d) {
  $a = explode("|", trim($d));

  $rulenum = $a[0];
  $mb_used = $a[1] / 1e6;

  $ip_src = $a[2];
  $ip_dst = $a[3];

  if( $mb_used < 1 ) continue;

  if( $ip_src != '0.0.0.0/0' ) {
    /* Upload MB */
    $dev = new Device($ip_src);

    exec("sudo iptables -Z FORWARD {$rulenum}");

    $db->mb_used = $mb_used;
    $db->mac_addr = $dev->mac;

    $db->get_device_id();
    $db->get_device_sid();

    $db->set_mb_used();

    $total_mb_limit = $db->get_total_mb_limit();
    $total_mb_used = $db->get_total_mb_used();

    printf("UL - ip: %s ; mb_used: %f\n", $ip_src, $mb_used );
  }

  if( $ip_dst != '0.0.0.0/0' ) {
    /* Download MB */
    $dev = new Device($ip_dst);

    exec("sudo iptables -Z FORWARD {$rulenum}");

    $db->mb_used = $mb_used;
    $db->mac_addr = $dev->mac;

    $db->get_device_id();
    $db->get_device_sid();

    $db->set_mb_used();

    $total_mb_limit = $db->get_total_mb_limit();
    $total_mb_used = $db->get_total_mb_used();

    printf("DL - ip: %s, mb_used: %f\n", $ip_dst, $mb_used );
  }

  sleep(1);
}

foreach($data as $d) {
  $a = explode("|", trim($d));

  $rulenum = $a[0];
  $mb_used = $a[1] / 1e6;

  $ip_src = $a[2];
  $ip_dst = $a[3];

  if( $ip_src != '0.0.0.0/0' ) {
    $dev = new Device($ip_src);

    if( !$dev->mac ) { echo "ip: {$ip_src} cannot resolved mac address.\n"; continue; }

    $db->mac_addr = $dev->mac;

    $db->get_device_id();

    $total_mb_limit = $db->get_total_mb_limit();
    $total_mb_used = $db->get_total_mb_used();

    if( $total_mb_limit <= $total_mb_used ) {
      $ipt = new Iptables($dev->ip, $dev->mac);
      $ipt->rem_client();
      printf("Disconnect ip: %s ; mb_limit: %f ; mb_used: %f\n", $ip_dst, $total_mb_limit, $total_mb_used );
    }
  }

  sleep(1);
}
