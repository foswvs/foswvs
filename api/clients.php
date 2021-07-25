#!/usr/bin/php
<?php
require __DIR__ . '/../lib/autoload.php';

$db = new Database();

$cmd = shell_exec("sudo iptables -nvxL FORWARD --line-numbers|awk 'NR>2{print $1 \"|\" $3 \"|\" $9 \"|\" $10}'");

if( empty($cmd) ) exit;

$data = explode("\n", trim($cmd));

$temp = [];

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
    usleep(1e5);
    $db->get_device_sid();
    usleep(1e5);
    $db->set_mb_used();

    printf("UL - ip: %s ; mb_used: %f\n", $ip_src, $mb_used );

    sleep(1);
  }

  if( $ip_dst != '0.0.0.0/0' ) {
    /* Download MB */
    $dev = new Device($ip_dst);

    exec("sudo iptables -Z FORWARD {$rulenum}");

    $db->mb_used = $mb_used;
    $db->mac_addr = $dev->mac;

    $db->get_device_id();
    usleep(1e5);
    $db->get_device_sid();
    usleep(1e5);
    $db->set_mb_used();
    usleep(1e5);

    $total_mb_limit = $db->get_total_mb_limit();
    $total_mb_used = $db->get_total_mb_used();

    printf("DL - ip: %s, mb_used: %f\n", $ip_dst, $mb_used );

    array_push($temp, ['ip_addr' => $ip_dst, 'mb_limit' => $total_mb_limit, 'mb_used' => $total_mb_used]);

    sleep(1);
  }
}

if( count($temp) == 0 ) exit;

foreach($temp as $d) {
  $ip_addr = $d['ip_addr'];
  $mb_limit = $d['mb_limit'];
  $mb_used = $d['mb_used'];

  printf("ip: %s ; mb_limit: %f ; mb_used: %f\n", $ip_addr, $mb_limit, $mb_used );

  if( $mb_limit <= $mb_used ) {
    while( shell_exec("sudo iptables -nL FORWARD | grep '{$ip_addr}'") ) {
      exec("sudo iptables -t nat -D PREROUTING -s {$ip_addr} -j ACCEPT");
      exec("sudo iptables -D FORWARD -d {$ip_addr} -j ACCEPT");
      exec("sudo iptables -D FORWARD -s {$ip_addr} -j ACCEPT");
      usleep(1e5);
    }
    printf("Disconnect ip: %s ; mb_limit: %f ; mb_used: %f\n", $d['ip'], $d['mb_limit'], $d['mb_used'] );
  }
}
