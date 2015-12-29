<?php

/**
 * @file
 * Getlocations_fields module integration.
 */

/**
 * Class mapFieldGetlocations.
 */
class mapFieldGetlocations extends mapFieldBase {
  /**
   * {@inheritdoc}
   */
  public function provideGeodata(&$items, &$settings) {
    $return = array();
    foreach ($items as $item) {
      $return[] = $item['latitude'] . ', ' . $item['longitude'];
    }
    return $return;
  }
}
