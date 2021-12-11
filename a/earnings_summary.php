<?php
require '../lib/database.php';

$db = new Database();

$sum = [];

$q = $db->query("SELECT IFNULL(SUM(piso_count),0) FROM session WHERE DATE(created_at,'+8 hours') = DATE('now','+8 hours')");
$sum['day'] = $q->fetchArray(SQLITE3_NUM)[0];

$q = $db->query("SELECT IFNULL(SUM(piso_count),0) FROM session WHERE DATE(created_at,'+8 hours') >= DATE('now','+8 hours','weekday 0','-7 days')");
$sum['week'] = $q->fetchArray(SQLITE3_NUM)[0];

$q = $db->query("SELECT IFNULL(SUM(piso_count),0) FROM session WHERE DATE(created_at,'+8 hours') >= DATE('now','+8 hours','start of month')");
$sum['month'] = $q->fetchArray(SQLITE3_NUM)[0];

$q = $db->query("SELECT IFNULL(SUM(piso_count),0) FROM session WHERE DATE(created_at,'+8 hours') >= DATE('now','+8 hours','start of year')");
$sum['year'] = $q->fetchArray(SQLITE3_NUM)[0];

$q = $db->query("SELECT IFNULL(SUM(piso_count),0) FROM session WHERE DATE(created_at,'+8 hours') = DATE('now','+8 hours','-1 day');");
$sum['last_day'] = $q->fetchArray(SQLITE3_NUM)[0];

$q = $db->query("SELECT IFNULL(SUM(piso_count),0) FROM session WHERE DATE(created_at,'+8 hours') BETWEEN DATE('now','+8 hours','weekday 0','-14 days') AND DATE('now','weekday 0','-8 days');");
$sum['last_week'] = $q->fetchArray(SQLITE3_NUM)[0];

$q = $db->query("SELECT IFNULL(SUM(piso_count),0) FROM session WHERE DATE(created_at,'+8 hours') BETWEEN DATE('now','+8 hours','start of month','-1 month') AND DATE('now','start of month','-1 day')");
$sum['last_month'] = $q->fetchArray(SQLITE3_NUM)[0];

$q = $db->query("SELECT IFNULL(SUM(piso_count),0) FROM session WHERE DATE(created_at,'+8 hours') BETWEEN DATE('now','+8 hours','start of year','-1 year') AND DATE('now','start of year','-1 day')");
$sum['last_year'] = $q->fetchArray(SQLITE3_NUM)[0];

echo json_encode($sum);
