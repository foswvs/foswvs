<?php
class Database extends SQLite3 {
  private $sid;
  private $devid;
  private $ip_addr;
  private $mac_addr;
  private $hostname;

  private $offset = 0;
  private $limit  = 25;

  private $mb_limit = 0;
  private $mb_used  = 0;

  private $piso_count = 0;

  public function __construct() {
    $dbf = '/home/pi/foswvs/conf/foswvs.db';

    $this->open($dbf);
    $this->exec("PRAGMA busy_timeout=10000");
    $this->exec("PRAGMA journal_mode=WAL;");

    $file = stat($dbf);

    if( $file['size'] <= 4096 ) {
      chmod($dbf, 0666);
      $this->create_table();
    }
  }

  public function __destruct() {
    $this->close();
  }

  public function set_mac($s) {
    if( filter_var($s, FILTER_VALIDATE_MAC) ) {
      $this->mac_addr = $s;
    }
  }

  public function set_ip($s) {
    if( filter_var($s, FILTER_VALIDATE_IP) ) {
      $this->ip_addr = $s;
    }
  }

  public function set_host($s) {
    $this->hostname = $s;
  }

  public function set_limit($n) {
    if( filter_var($n, FILTER_VALIDATE_INT) ) {
      $this->limit = $n;
    }
  }

  public function set_offset($n) {
    if( filter_var($n, FILTER_VALIDATE_INT) ) {
      $this->offset = $n;
    }
  }

  public function set_mb_limit($f) {
    if( filter_var($f, FILTER_VALIDATE_FLOAT) ) {
      $this->mb_limit = $f;
    }
  }

  public function set_mb_used($f) {
    if( filter_var($f, FILTER_VALIDATE_FLOAT) ) {
      $this->mb_used = $f;
    }
  }

  public function set_amount($f) {
    if( filter_var($f, FILTER_VALIDATE_FLOAT) ) {
      $this->piso_count = $f;
    }
  }

  public function set_did($n) {
    if( filter_var($n, FILTER_VALIDATE_INT) ) {
      $this->devid = $n;
    }
  }

  public function set_sid($n) {
    if( filter_var($n, FILTER_VALIDATE_INT) ) {
      $this->sid = $n;
    }
  }

  public function get_did() {
    return $this->devid;
  }

  public function get_sid() {
    return $this->sid;
  }

  public function create_table() {
    $this->exec("CREATE TABLE IF NOT EXISTS devices (id INTEGER PRIMARY KEY AUTOINCREMENT, mac_addr TEXT NOT NULL UNIQUE, ip_addr DEFAULT '127.0.0.1', hostname DEFAULT '-NA-', topup_count DEFAULT 0, created_at DEFAULT CURRENT_TIMESTAMP, updated_at DEFAULT CURRENT_TIMESTAMP, topup_at DEFAULT CURRENT_TIMESTAMP);");
    $this->exec("CREATE TABLE IF NOT EXISTS session (id INTEGER PRIMARY KEY AUTOINCREMENT, device_id INTEGER, piso_count DEFAULT 0, mb_limit DEFAULT 0, mb_used DEFAULT 0, created_at DEFAULT CURRENT_TIMESTAMP, updated_at DEFAULT CURRENT_TIMESTAMP);");
    $this->exec("CREATE TABLE sharetx (id INTEGER PRIMARY KEY AUTOINCREMENT, device_id INTEGER,token TEXT, created_at DEFAULT CURRENT_TIMESTAMP);");
  }

  public function get_devices() {
    $cmd = $this->query("SELECT mac_addr AS mac, IFNULL(ip_addr,'-NA-') AS ip, IFNULL(hostname,'-NA-') AS host,strftime('%s', updated_at) * 1000 AS updated_at FROM devices WHERE mac_addr!='' ORDER BY updated_at DESC");

    $res = [];

    while( $row = $cmd->fetchArray(SQLITE3_ASSOC) ) {
      array_push($res, $row);
    }

    return $res;
  }

  public function add_device() {
    $this->exec("INSERT INTO devices(mac_addr,ip_addr,hostname) VALUES('{$this->mac_addr}','{$this->ip_addr}','{$this->hostname}')");

    $this->devid = $this->lastInsertRowID();
  }

  public function update_device() {
    $this->exec("UPDATE devices SET hostname='{$this->hostname}',ip_addr='{$this->ip_addr}',topup_count=0,updated_at=CURRENT_TIMESTAMP WHERE id='{$this->devid}'");
  }

  public function get_device_id() {
    $res = $this->query("SELECT id FROM devices WHERE mac_addr='{$this->mac_addr}'");
    $row = $res->fetchArray(SQLITE3_ASSOC);

    return $this->devid = $row['id'];
  }

  public function get_device_id_by_ip() {
    $res = $this->query("SELECT id FROM devices WHERE ip_addr='{$this->ip_addr}' ORDER BY updated_at DESC LIMIT 1");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $this->devid = $row[0];
  }

  public function get_device_ip() {
    $res = $this->query("SELECT ip_addr FROM devices WHERE id='{$this->devid}'");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $row[0];
  }

  public function get_device_ip_connected_before() {
    $cmd = $this->query("SELECT ip_addr FROM devices WHERE id='{$this->devid}' AND updated_at > DATETIME(CURRENT_TIMESTAMP,'-12 hours')");

    return $cmd->fetchArray(SQLITE3_NUM)[0];
  }

  public function get_device_mac() {
    $res = $this->query("SELECT mac_addr FROM devices WHERE id='{$this->devid}'");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $row[0];
  }

  public function get_active_at() {
    $cmd = $this->query("SELECT strftime('%s',updated_at) * 1000 AS active_at FROM session WHERE device_id={$this->devid} ORDER BY updated_at DESC LIMIT 1");
    $res = $cmd->fetchArray(SQLITE3_NUM);
    return $res[0];
  }

  public function get_device_info() {
    $cmd = $this->query("SELECT mac_addr AS mac, IFNULL(ip_addr,'-NA-') AS ip, IFNULL(hostname,'-NA-') AS host FROM devices WHERE id='{$this->devid}'");

    return $cmd->fetchArray(SQLITE3_ASSOC);
  }

  public function get_data_usage() {
    $cmd = $this->query("SELECT SUM(mb_limit) AS total_mb_limit, SUM(mb_used) AS total_mb_used FROM session WHERE device_id={$this->devid}");

    return $cmd->fetchArray(SQLITE3_NUM);
  }

  public function add_session() {
    $this->exec("INSERT INTO session(device_id,piso_count,mb_limit) VALUES({$this->devid},{$this->piso_count},{$this->mb_limit})");

    $this->sid = $this->lastInsertRowID();
  }

  public function rem_session() {
    $this->exec("DELETE FROM session WHERE id={$this->sid}");
  }

  public function update_mb_used() {
    $this->exec("UPDATE session SET mb_used=mb_used+{$this->mb_used},updated_at=CURRENT_TIMESTAMP WHERE device_id={$this->devid} AND mb_limit > mb_used LIMIT 1");
  }

  public function get_total_mb_limit() {
    $res = $this->query("SELECT SUM(mb_limit) FROM session WHERE device_id={$this->devid}");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $row[0] ? $row[0] : 0;
  }

  public function get_total_mb_used() {
    $res = $this->query("SELECT sum(mb_used) FROM session WHERE device_id={$this->devid}");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $row[0] ? $row[0] : 0;
  }

  public function clear_mb() {
    $this->exec("DELETE FROM session WHERE device_id={$this->devid}");
  }

  public function get_all_txn() {
    $cmd = $this->query("SELECT s.piso_count AS amt,s.mb_limit AS mb,strftime('%s',s.created_at) * 1000 AS ts,d.mac_addr AS mac,d.ip_addr AS ip,d.hostname AS host FROM session s LEFT JOIN devices d ON s.device_id=d.id ORDER BY s.id DESC LIMIT {$this->offset},{$this->limit}");

    $res = [];

    while( $row = $cmd->fetchArray(SQLITE3_ASSOC) ) {
      array_push($res, $row);
    }

    return $res;
  }

  public function get_device_sessions() {
    $cmd = $this->query("SELECT id,piso_count AS amt,mb_limit,mb_used,strftime('%s',created_at) * 1000 AS ts,strftime('%s',updated_at) * 1000 AS te FROM session WHERE device_id={$this->devid} ORDER BY id DESC");

    $res = [];

    while( $row = $cmd->fetchArray(SQLITE3_ASSOC) ) {
      array_push($res, $row);
    }

    return $res;
  }

  public function get_active_devices() {
    $cmd = $this->query("SELECT d.mac_addr AS mac, d.ip_addr AS ip, IFNULL(d.hostname,'-NA-') AS host,strftime('%s',s.updated_at) * 1000 AS updated_at FROM session s JOIN devices d ON d.id=s.device_id WHERE s.updated_at > DATETIME(CURRENT_TIMESTAMP,'-1 minute') ORDER BY updated_at DESC");

    $res = [];

    while( $row = $cmd->fetchArray(SQLITE3_ASSOC) ) {
      array_push($res, $row);
    }

    return $res;
  }

  public function get_recent_devices() {
    $cmd = $this->query("SELECT mac_addr AS mac, IFNULL(ip_addr,'-NA-') AS ip, IFNULL(hostname,'-NA-') AS host,strftime('%s',updated_at) * 1000 AS updated_at FROM devices WHERE updated_at > DATETIME(CURRENT_TIMESTAMP,'-4 HOURS') ORDER BY updated_at DESC");

    $res = [];

    while( $row = $cmd->fetchArray(SQLITE3_ASSOC) ) {
      array_push($res, $row);
    }

    return $res;
  }

  public function get_restricted_devices() {
    $cmd = $this->query("SELECT mac_addr AS mac,ip_addr AS ip,IFNULL(hostname,'-NA-') AS host,(SELECT strftime('%s',updated_at) * 1000 FROM session WHERE device_id=devices.id ORDER BY updated_at DESC LIMIT 1) AS active_at,(SELECT sum(mb_limit)-sum(mb_used) FROM session WHERE device_id=devices.id) AS mb_free FROM devices WHERE mb_free <= 0 ORDER BY active_at DESC");

    $res = [];

    while( $row = $cmd->fetchArray(SQLITE3_ASSOC) ) {
      array_push($res, $row);
    }

    return $res;
  }

  public function update_session_from_random_mac($MAC) {
    $this->exec("UPDATE session SET device_id={$this->devid} WHERE id IN (SELECT id FROM session WHERE device_id IN (SELECT id FROM devices WHERE mac_addr='{$MAC}'))");
  }

  public function get_topup_count() {
    $q = $this->query("SELECT topup_count FROM devices WHERE id={$this->devid}");

    return $q->fetchArray(SQLITE3_NUM)[0];
  }

  public function set_topup_count() {
    $this->exec("UPDATE devices SET topup_count=topup_count+1, topup_at=CURRENT_TIMESTAMP WHERE id={$this->devid}");
  }

  public function add_sharetx($tok) {
    $this->exec("INSERT INTO sharetx(device_id,token) VALUES({$this->devid},'{$tok}')");
  }

  public function get_sharetx_did($tok) {
    $cmd = $this->query("SELECT device_id FROM sharetx WHERE token='{$tok}' ORDER BY id DESC  LIMIT 1");

    return $cmd->fetchArray(SQLITE3_NUM)[0];
  }

  public function rem_sharetx($tok) {
    $this->exec("DELETE FROM sharetx WHERE token='{$tok}'");
  }

  public function clear_sharetx() {
    $this->exec("DELETE FROM sharetx WHERE created_at < DATETIME(CURRENT_TIMESTAMP,'-1 MINUTE')");
  }
}
