<?php
class Helper {
  public function format_mb($size) {

    if( $size < 1 ) return '0MB';

    $base = floor(log($size, 1024));
    $unit = array('MB','GB','TB','PB','EB','ZB','YB');

    return round($size / pow(1024, $base), 2) . $unit[$base];
  }

  public function amount_mb($peso) {
    $size = 0;
    $rates = json_decode(file_get_contents(__DIR__ . '/../conf/rates.json'), true);

    krsort($rates);

    foreach($rates as $amt=>$val) {
      if( $peso >= $amt ) {
        $base = floor($peso / $amt);
        $size = $base * $val + $size;
        $peso = $peso - ($base * $amt);
      }
    }

    return $size;
  }
}
