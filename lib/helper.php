<?php
class Helper {
  public function format_mb($size, $precision = 2) {

    if( !$size ) return $size . 'MB';

    $base = log($size, 1024);
    $suffixes = array('MB','GB','TB','PB','EB','ZB','YB');

    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
  }

  public function amt_to_mb($total_amt) {
    $data = 0; $five = 5; $ten = 10;

    if( $total_amt >= $ten ) {
      $base = floor($total_amt / $ten);

      $data = $base * 1500;

      $amt_count = $base * $ten;

      $total_amt = $total_amt - $amt_count;
    }

    if( $total_amt >= $five ) {
      $base = floor($total_amt / $five);

      $data = $base * 500 + $data;

      $amt_count = $base * $five;

      $total_amt = $total_amt - $amt_count;
    }

    return ($total_amt * 50) + $data;
  }
}
