<?php
class System {
  public function cpu_temp() {
    return floatval(file_get_contents('/sys/class/thermal/thermal_zone0/temp'))/1000;
  }

  public function cpu_frequency() {
    return floatval(file_get_contents('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq'))/1000;
  }
}
