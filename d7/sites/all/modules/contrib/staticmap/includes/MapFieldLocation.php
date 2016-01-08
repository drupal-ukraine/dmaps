<?php

/**
 * @file
 * Location module integration.
 */

/**
 * Class MapFieldLocation
 */
class MapFieldLocation extends MapFieldBase {
  /**
   * Geodata provider.
   *
   * @inheritdoc
   */
  public function provideGeodata(&$items, &$settings) {
    $return = array();
    foreach ($items as $item) {
      $return[] = $item['latitude'] . ', ' . $item['longitude'];
    }

    return $return;
  }
}
