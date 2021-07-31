<?php
class System {
  public function uptime() {
    return exec("uptime -p");
  }

  public function cpu_temp() {
    return floatval(file_get_contents('/sys/class/thermal/thermal_zone0/temp'))/1000;
  }

  public function cpu_frequency() {
    return floatval(file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq'))/1000;
  }

  public function mem_usage() {
    return exec("free -m | awk '/Mem:/ { total=$2 ; used=$3 } END { print used/total*100}'");
  }

  public function interfaces() {
    exec("ls /sys/class/net | grep -v lo", $interfaces);

    return json_encode($interfaces);
  }
}
