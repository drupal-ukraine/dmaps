<?php
/**
 * @file
 * Geocoder services.
 */

namespace Drupal\dmaps;

/**
 * Class DmapsGeocoder.
 *
 * @package Drupal\dmaps
 */
class DmapsGeocoder {

  /**
   * Implement location_map_link_providers().
   *
   * @return array
   */
  public function getMapProviders() {
    // @todo 8.x-2.x - Convert to plugins.
    return array(
      'google' => array(
        'name' => t('Google Maps'),
        'url' => 'http://maps.google.com',
        'tos' => 'http://www.google.com/help/terms_local.html',
      ),
    );
  }

}