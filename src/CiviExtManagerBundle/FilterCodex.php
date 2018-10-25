<?php
namespace CiviExtManagerBundle;

/**
 * Class FilterCodex
 * @package CiviExtManagerBundle
 *
 * This simply holds utility functions for encoding and ecoding
 * filter expressions.
 */
class FilterCodex {

  /**
   * @param string $str
   *   Ex: 'ver=1.2.3|cms=Foo|ready='
   * @return array
   */
  public static function decode($str) {
    $filters = [];
    $pairs = explode('|', $str);
    foreach ($pairs as $pair) {
      list ($key, $value) = explode('=', $pair, 2);
      $filters[$key] = $value;
    }
    return $filters;
  }

}
