#!/usr/bin/php
<?php
require __DIR__ . '/autoload.php';

$db = new Database();

$cmd = shell_exec("sudo iptables -nvxL FORWARD|awk 'NR>2{print $2 \"\t\" $8 \"\t\" $9}'");

$data = explode("\n", trim($cmd));

foreach($data as $d) {
  $a = explode("\t", trim($d));
  $b = $a[0] / 1e6;

  if( $b > 1 && $a[1] != '0.0.0.0/0' ) {
    /* Upload MB */
    //printf("upload: %d [%s] \n", $b, $a[1]);
  }

  if( $b > 1 && $a[2] != '0.0.0.0/0' ) {
    /* Download MB */
    $dev = new Device($a[2]);

    $db->mb_used = $b;
    $db->mac_addr = $dev->mac;

    $db->get_device_id();
    $db->get_device_sid();

    $db->set_mb_used();

    printf("ip: %s, mac: %s ; devid: %d ; sid: %d ; mb_used: %d\n", $dev->ip, $dev->mac, $db->devid, $db->sid, $db->mb_used);

    if( $db->get_total_mb_limit() <= $db->get_total_mb_used() ) {
      $ipt = new Iptables($dev->ip, $dev->mac);
      $ipt->rem_client();
    }
    sleep(1);
  }
}
