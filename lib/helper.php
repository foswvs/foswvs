<?php
class Helper {
  public function format_mb($size, $precision = 2) {

    if( !$size ) return $size . 'MB';

    $base = log($size, 1024);
    $suffixes = array('MB','GB','TB','PB','EB','ZB','YB');

    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
  }

  public function amt_to_mb($amt) {
    $data = 0;
    $rates = [1 => 50, 5 => 500, 10 => 1024, 30 => 4096];

    krsort($rates);

    foreach($rates as $r_amt=>$r_data) {
      if( $amt >= $r_amt ) {
        $base = floor($amt / $r_amt);
        $data = $base * $r_data + $data;
        $amt = $amt - ($base * $r_amt);
      }
    }

    return $data;
  }
}
