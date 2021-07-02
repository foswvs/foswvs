<?php
class Database extends SQLite3 {
  function __construct() {
    $this->open('foswvs.db');
  }

  public function create_table() {
    $this->exec("CREATE TABLE devices (id INTEGER PRIMARY KEY AUTOINCREMENT, mac_addr BLOB UNIQUE, ip_addr BLOB, session_id INT, created_at DEFAULT CURRENT_TIMESTAMP, updated_at DEFAULT CURRENT_TIMESTAMP)");
    $this->exec("CREATE TABLE session (id INTEGER PRIMARY KEY AUTOINCREMENT, device_id INT, piso_count INT DEFAULT 0, mb_credit INT DEFAULT 0, mb_used INT DEFAULT 0, created_at DEFAULT CURRENT_TIMESTAMP, updated_at DEFAULT CURRENT_TIMESTAMP)");
  }

  public function add_session($device_id, $piso_count, $mb_credit) {
    $this->exec("INSERT INTO session(device_id, piso_count, mb_credit) VALUES({$device_id}, {$piso_count}, {$mb_credit})");
  }

  public function get_session($device_id) {
    $res = $this->query("SELECT id FROM session WHERE device_id={$device_id} ORDER BY DESC LIMIT 1");
    $row = $res->fetchArray(SQLITE3_ASSOC);

    return $row['id'];
  }

  public function get_piso_count($session) {
    $res = $this->query("SELECT piso_count FROM session WHERE session_id={$session_id}");
    $row = $res->fetchArray(SQLITE3_ASSOC);

    return $row['piso_count'];
  }

  public function add_device($mac_addr, $ip_addr) {
    $this->exec("INSERT INTO devices(mac_addr, ip_addr) VALUES('{$mac_addr}', '{$ip_addr}')");
  }

  public function get_device_id($mac_addr) {
    $res = $this->query("SELECT id FROM devices WHERE mac_addr='{$mac_addr}'");
    $row = $res->fetchArray(SQLITE3_ASSOC);

    return $row['id'];
  }

  public function get_mb_credit($session_id) {
    $res = $this->query("SELECT mb_credit FROM session WHERE session_id={$session_id}");
    $row = $res->fetchArray(SQLITE3_ASSOC);

    return $row['mb_credit'];
  }

  public function set_mb_used($session_id, $mb_used) {
    $this->exec("UPDATE session SET mb_used=(mb_used + {$mb_used}) WHERE session_id={$session_id}");
  }

  public function find_available_session($device_id) {
    $res = $this->query("SELECT id FROM session WHERE mb_credit > mb_used AND device_id={$device_id} LIMIT 1");
    $row = $res->fetchArray(SQLITE3_ASSOC);

    return $row['id'];
  }
}
