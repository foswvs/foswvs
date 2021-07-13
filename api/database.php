<?php
class Database extends SQLite3 {
  public $sid;
  public $devid;
  public $ip_addr;
  public $mac_addr;
  public $hostname;
  public $updated_at;

  public $offset = 0;
  public $limit  = 25;

  public $mb_limit = 0;
  public $mb_used  = 0;

  public $piso_count = 0;

  public function __construct() {
    $dbf = __DIR__ . '/foswvs.db';

    $this->open($dbf);
    $this->exec("PRAGMA busy_timeout=300");
    $this->exec("PRAGMA journal_mode=WAL;");

    $file = stat($dbf);

    if( $file['size'] <= 4096 ) {
      chmod($dbf, 0777);
      $this->create_table();
    }
  }

  public function __destruct() {
    $this->close();
  }

  public function create_table() {
    $this->exec("CREATE TABLE devices (id INTEGER PRIMARY KEY AUTOINCREMENT, mac_addr BLOB UNIQUE, ip_addr BLOB, hostname BLOB, session_id INTEGER, created_at DEFAULT CURRENT_TIMESTAMP, updated_at DEFAULT CURRENT_TIMESTAMP)");
    $this->exec("CREATE TABLE session (id INTEGER PRIMARY KEY AUTOINCREMENT, device_id INTEGER, piso_count INTEGER DEFAULT 0, mb_limit INTEGER DEFAULT 0, mb_used INTEGER DEFAULT 0, created_at DEFAULT CURRENT_TIMESTAMP, updated_at DEFAULT CURRENT_TIMESTAMP)");
  }

  public function add_device() {
    $this->exec("INSERT INTO devices(mac_addr) VALUES('{$this->mac_addr}')");

    $this->devid = $this->lastInsertRowID();
  }

  public function set_device() {
    $this->exec("UPDATE devices SET hostname={$this->hostname},ip_addr={$this->ip_addr},updated_at={$this->updated_at} WHERE mac_addr={$this->mac}");
  }

  public function get_device_id() {
    $res = $this->query("SELECT id FROM devices WHERE mac_addr='{$this->mac_addr}'");
    $row = $res->fetchArray(SQLITE3_ASSOC);

    return $this->devid = $row['id'];
  }

  public function set_device_session() {
    $this->exec("UPDATE devices SET session_id={$this->sid} WHERE id={$this->devid}");
  }

  public function get_device_session() {
    $res = $this->query("SELECT session_id FROM devices WHERE id={$this->devid}");
    $row = $res->fetchArray(SQLITE3_NUM);

    $this->sid = $row[0] ? $row[0] : 0;

    return $this->sid;
  }

  public function add_session() {
    $this->exec("INSERT INTO session(device_id) VALUES({$this->devid})");

    $this->sid = $this->lastInsertRowID();
  }

  public function set_piso_count() {
    $this->exec("UPDATE session SET piso_count={$this->piso_count} WHERE id={$this->sid}");
  }

  public function get_piso_count() {
    $res = $this->query("SELECT piso_count FROM session WHERE id={$this->sid}");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $row[0] ? $row[0] : 0;
  }

  public function set_mb_limit() {
    $this->exec("UPDATE session SET mb_limit={$this->mb_limit} WHERE id={$this->sid}");
  }

  public function set_mb_used() {
    $this->exec("UPDATE session SET mb_used={$this->mb_used} WHERE id={$this->sid}");
  }

  public function get_mb_limit() {
    $res = $this->query("SELECT mb_limit FROM session WHERE id={$this->sid}");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $row[0] ? $row[0] : 0;
  }

  public function get_total_mb_limit() {
    $res = $this->query("SELECT SUM(mb_limit) FROM session WHERE device_id={$this->devid}");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $row[0] ? $row[0] : 0;
  }

  public function get_mb_used() {
    $res = $this->query("SELECT mb_used FROM session WHERE id={$this->sid}");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $row[0] ? $row[0] : 0;
  }

  public function get_total_mb_used() {
    $res = $this->query("SELECT sum(mb_used) FROM session WHERE device_id={$this->devid}");
    $row = $res->fetchArray(SQLITE3_NUM);

    return $row[0] ? $row[0] : 0;
  }

  public function get_all_txn() {
    $cmd = $this->query("SELECT s.piso_count AS amt,s.mb_limit AS mb,s.created_at AS ts,d.mac_addr AS mac,d.id AS devId FROM session s LEFT JOIN devices d ON s.device_id=d.id ORDER BY s.id DESC LIMIT {$this->offset},{$this->limit}");

    $res = [];

    while( $row = $cmd->fetchArray(SQLITE3_ASSOC) ) {
      array_push($res, $row);
    }

    return $res;
  }
}
