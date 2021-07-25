<?php
require '../lib/autoload.php';

$IP = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

$dev = new Device($IP);

$ipt = new Iptables($dev->ip, $dev->mac);

echo $ipt->connected();
