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
   * Provide default map link providers.
   *
   * By implementing location_map_link_COUNTRYCODE_providers, individual
   * countries can add to this (they will be merged so this default can also be
   * overridden using those functions).
   *
   * @return array
   *   An array where
   *    - the key is the a descriptive key for the provider.
   *      The key is also used in the function name of the function that
   *      generates the map links for that provider. For example, a key of
   *      'google' means the name of the function that builds a link to a map
   *      Google Maps would be 'location_map_link_google', or
   *      'location_map_link_COUNTRYCODE_google' for country overrides.
   *    - the value is itself an array with 3 key/value pairs:
   *      - 'name' => points to the name of the mapping service.  For 'google',
   *        this would be 'Google Maps'
   *      - 'url' => the url of the main page of the mapping service.  For
   *        'google', this would be 'http://maps.google.com'
   *      - 'tos' => the url of the page that explains the map providers Terms of
   *        Service, or Terms of Use. For 'google', this would be
   *        'http://www.google.com/help/terms_local.html'
   */
  public function getMapProviders() {
    // Implements location_map_link_providers().
    // @todo 8.x-2.x - Convert to plugins.
    return array(
      'google' => array(
        'name' => t('Google Maps'),
        'url' => 'http://maps.google.com',
        'tos' => 'http://www.google.com/help/terms_local.html',
      ),
    );
  }

  /**
   * Provide the default map link providers.
   *
   * // @todo This can be overridden by implementing
   * // @todo location_map_link_COUNTRYCODE_default_providers for a given country.
   *
   * @return array
   *   An array of values that work as keys to the array returned by
   *   location_map_link_providers (and country versions of that function).
   *   The idea is that if the administrator of the site has not yet had a chance
   *   to visit the "Map Links" subtab on the location module's settings page,
   *   that we can provide deep-linking to a relatively safe default.
   *   By 'relatively safe', we mean that the Terms Of Service of the provider
   *   of the maps are flexible enough for most parties.
   *
   *   For example, in the case of the U.S., 'google' has relatively flexible
   *   Terms Of Service, whereas Yahoo! Maps and MapQuest have more restrictive
   *   Terms Of Service.
   */
  public function getDefaultMapProviders() {
    // Implements location_map_link_default_providers().
    // @todo 8.x-2.x - convert all providers to plugins.
    return array('google');
  }

  /**
   * Form a Google link.
   *
   * @param array $location
   *   Location to be processed.
   *
   * @return null|string
   *   URL string if ok, NULL if error.
   */
  public function getGoogleLink($location = array()) {
    // Implements location_map_link_google().
    // @todo 8.x-2.x - should be a part of Google provider plugin.
    $query_params = array();

    foreach (array('street', 'city', 'province', 'postal_code', 'country') as $field) {
      if (isset($location[$field])) {
        $query_params[] = $location[$field];
      }
    }

    if (count($query_params)) {
      return ('http://maps.google.com?q=' . urlencode(implode(", ", $query_params)));
    }
    else {
      return NULL;
    }
  }

  /**
   * Make country code canonical.
   *
   * @param string $country
   *   Country code.
   *
   * @return bool
   *   False if wrong, TRUE if ok.
   */
  public function setStdCountryCode(&$country) {
    // Implements location_standardize_country_code().
    $country = trim($country);
    // @todo 8.x-1.x Double check the validity of this validity check. ;)
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
  public function getCountry($country) {
    // Implements location_load_country().
    $this->setStdCountryCode($country);

    // @todo 8.x-2.x - convert to country plugins. Or even configs.
    $file = DRUPAL_ROOT . '/' . drupal_get_path('module', 'dmaps') . '/supported/location.' . $country . '.inc';
    if (file_exists($file)) {
      include_once $file;

      return TRUE;
    }

    return FALSE;
  }

}