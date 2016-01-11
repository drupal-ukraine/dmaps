<?php

/**
 * @file
 * Contains \Drupal\dmaps\LocationCountriesManager.
 */

namespace Drupal\dmaps;

use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Defines a location countries manager service.
 */
class LocationCountriesManager implements LocationCountriesManagerInterface {

  /**
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   Cache service.
   */
  public function __construct(CacheBackendInterface $cache) {
    $this->cache = $cache;
  }

  /**
   * @inheritdoc
   */
  public function getSupportedList() {
    // Try first to load from cache, it's much faster than the scan below.
    if ($cache = $this->cache->get('location:supported-countries')) {
      $supported_countries = $cache->data;
    }
    else {
      // '<ISO two-letter code>' => '<English name for country>'
      $iso_list = static::getIso3166List();
      $path = drupal_get_path('module', 'dmaps') . '/files/supported_countries/';
      foreach ($iso_list as $cc => $name) {
        if (file_exists($path . $cc . '.inc')) {
          $supported_countries[$cc] = $name;
        }
      }
      $this->cache->set('location:supported-countries', $supported_countries);
    }
    return $supported_countries;
  }

  /**
   * Remove cached list of supported countries and rebuild it.
   */
  public function rebuildSupportedList() {
    $this->cache->delete('location:supported-countries');
    $this->getSupportedList();
  }

  /**
   * Get list of ISO 3166-1 alpha-2 countries.
   *
   * @return array
   *   List of all countries in format country-code => country-name.
   */
  public static function getIso3166List() {
    $countries = \Drupal::service('country_manager')->getList();

    // Module uses lower-case ISO 3166-1 alpha2 codes, so we need to convert.
    $countries = array_change_key_case($countries, CASE_LOWER);

    return $countries;
  }
}
