<?php

/**
 * @file
 * Geofield provider.
 */

/**
 * Class MapFieldGeofield
 */
class MapFieldGeofield extends MapFieldBase {
  /**
   * Geodata provider.
   *
   * @inheritdoc
   */
  public function provideGeodata(&$items, &$settings) {
    $return = array();
    foreach ($items as $item) {
      $return[] = $item['lat'] . ', ' . $item['lon'];
    }

    return $return;
  }
}
