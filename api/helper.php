<?php
class Helper {
  public function format_mb($size, $precision = 2) {

    if( !$size ) return $size . 'MB';

    $base = log($size, 1024);
    $suffixes = array('MB','GB','TB','PB','EB','ZB','YB');

    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
  }
}
