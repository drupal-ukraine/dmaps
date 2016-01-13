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
    $supported_countries = &drupal_static(__FUNCTION__, array());

    // If this function has already been called this request, we can avoid a DB hit.
    if (!empty($supported_countries)) {
      return $supported_countries;
    }

    // Try first to load from cache, it's much faster than the scan below.
    if ($cache = $this->cache->get('location:supported-countries')) {
      $supported_countries = $cache->data;
    }
    else {
      // '<ISO two-letter code>' => '<English name for country>'
      $iso_list = static::getIso3166List();
      $path = DRUPAL_ROOT . '/' . drupal_get_path('module', 'dmaps') . '/files/supported_countries/location.';
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
   * @param bool $upper
   *   Indicate return codes in upper case or not.
   *
   * @return array
   *   List of all countries in format country-code => country-name.
   */
  public static function getIso3166List($upper = FALSE) {
    // Statically cache a version of the core Drupal list of countries
    // with lower case country codes for use by this module.
    $countries = &drupal_static(__FUNCTION__);
    if ($upper) {
      // Drupal core stores ISO 3166-1 alpha2 codes in upper case, as
      // per the ISO standard.
      return \Drupal::service('country_manager')->getList();
    }
    elseif (!isset($countries)) {
      $countries = \Drupal::service('country_manager')->getList();
      // Module uses lower-case ISO 3166-1 alpha2 codes, so we need to convert.
      $countries = array_change_key_case($countries, CASE_LOWER);
    }

    return $countries;
  }

  /**
   * Load support for a country.
   *
   * This function will load support for a country identified by its two-letter ISO code.
   *
   * @param string $country
   *   Two-letter ISO code for country.
   *
   * @return bool
   *   TRUE if the file was found and loaded, FALSE otherwise.
   */
  public static function locationLoadCountry($country) {
    static::locationStandardizeCountryCode($country);

    $file = DRUPAL_ROOT . '/' . drupal_get_path('module', 'dmaps') . '/files/supported_countries/location.' . $country . '.inc';
    if (file_exists($file)) {
      include_once $file;

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Canonicalize a country code.
   *
   * @param string $country
   *   Two-letter ISO code for country.
   *
   * @return bool
   */
  public static function locationStandardizeCountryCode(&$country) {
    $country = trim($country);
    // Double check the validity of this validity check.
    if (!ctype_alpha($country) || strlen($country) != 2) {
      $country = 'xx';

      return FALSE;
    }
    else {
      $country = strtolower($country);

      return TRUE;
    }
  }

  /**
   * Get the translated name of a country code.
   *
   * @param string $country
   *   Two-letter ISO code for country.
   *
   * @return string
   */
  public function locationCountryName($country = 'us') {
    static::locationStandardizeCountryCode($country);
    $countries = static::getIso3166List();
    if (isset($countries[$country])) {

      return $countries[$country];
    }

    return '';
  }
}
