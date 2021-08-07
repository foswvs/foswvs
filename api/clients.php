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
  $ip = NULL;

  $rulenum = $a[0];
  $mb_used = $a[1] / 1e6;

  $ip_src = $a[2];
  $ip_dst = $a[3];

  /* non-active user */
  if( $mb_used == 0 ) {
    echo "\nidle: $mb_used | $ip_src | $ip_dst";
    continue;
  }

  /* do not capture <1Mbps */
  if( $mb_used < 0.125 ) {
    echo "\nlow usage: $mb_used | $ip_src | $ip_dst";
    continue;
  }

  /* Upload MB */
  if( $ip_src != '0.0.0.0/0' ) {
    $ip = $ip_src; exec("sudo iptables -Z FORWARD {$rulenum}");
  }

  /* Download MB */
  if( $ip_dst != '0.0.0.0/0' ) {
    $ip = $ip_dst; exec("sudo iptables -Z FORWARD {$rulenum}");
  }

  array_push($temp, ['ip' => $ip, 'mb' => $mb_used]);
}

if( count($temp) == 0 ) exit;

$tmp = [];
echo "\n";

foreach($temp as $d) {
  $ip = $d['ip'];
  $mb = $d['mb'];

  $dev = new Device($ip);

  $db->mb_used = $mb;
  $db->mac_addr = $dev->mac;

  $db->get_device_id();
  usleep(1e5);
  $db->set_mb_used();
  usleep(1e5);

  list($mb_limit,$mb_used) = $db->get_data_usage();

  $arr = ['ip_addr' => $ip, 'mb_rxtx' => $mb, 'mb_limit' => $mb_limit, 'mb_used' => $mb_used];

  print_r($arr);

  array_push($tmp, $arr);

  sleep(1);
}

foreach($tmp as $d) {
  $ip_addr = $d['ip_addr'];
  $mb_rxtx = $d['mb_rxtx'];
  $mb_used = $d['mb_used'];
  $mb_limit = $d['mb_limit'];

  printf("\nip: %s ; mb_rxtx: %f; mb_limit: %f; mb_used: %f", $ip_addr, $mb_rxtx, $mb_limit, $mb_used );

  if( $mb_limit <= $mb_used ) {
    sleep(1);
    while( shell_exec("sudo iptables -nL FORWARD | grep '{$ip_addr}'") ) {
      exec("sudo iptables -t nat -D PREROUTING -s {$ip_addr} -j ACCEPT");
      exec("sudo iptables -D FORWARD -d {$ip_addr} -j ACCEPT");
      exec("sudo iptables -D FORWARD -s {$ip_addr} -j ACCEPT");
      sleep(1);
    }
    echo "- Disconnected";
  }
}

exit;
