<?php

/**
 * @file
 * Contains \Drupal\dmaps\DmapsLocationCountriesManager.
 */

namespace Drupal\dmaps;

use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Defines a location countries manager service.
 */
class DmapsLocationCountriesManager {

  /**
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   Cache service.
   */
  public function __construct(CacheBackendInterface $cache) {
    $this->cache = $cache;
  }

  /**
   * Get list of ISO 3166-1 alpha-2 countries.
   *
   * @return array
   *   List of all countries in format country-code => country-name.
   */
  public function getIso3166List() {
    $countries = \Drupal::service('country_manager')->getList();

    // Location module uses lower-case ISO 3166-1 alpha2 codes, so we need
    // to convert.
    $countries = array_change_key_case($countries, CASE_LOWER);

    return $countries;
  }
}
