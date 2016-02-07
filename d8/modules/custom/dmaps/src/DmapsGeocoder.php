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
      'google' => [
        'name' => t('Google Maps'),
        'url' => 'http://maps.google.com',
        'tos' => 'http://www.google.com/help/terms_local.html',
      ],
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
    return ['google'];
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
  public function getGoogleLink($location = []) {
    // Implements location_map_link_google().
    // @todo 8.x-2.x - should be a part of Google provider plugin.
    $query_params = [];

    foreach (['street', 'city', 'province', 'postal_code', 'country'] as $field)
    {
      if (isset($location[$field])) {
        $query_params[] = $location[$field];
      }
    }

    if (count($query_params)) {
      return ('http://maps.google.com?q=' . urlencode(implode(", ", $query_params)));
    }
    return NULL;
  }

  /**
   * Load a general geocoding service.
   */
  public function initGeocoder($geocoder) {
    // Implements location_load_geocoder();
    // @todo 8.x-1.x - change to trait.
    include_once DRUPAL_ROOT . '/' . drupal_get_path('module', 'dmaps') . '/geocoding/' . $geocoder . '.inc';
  }

  /**
   * Create a single line address.
   *
   * @param array $location
   *   The address parts.
   *
   * @return string
   *   The single line address.
   */
  function getAddrSingleline($location = []) {
    // Implements location_address2singleline().
    // @todo 8.x-1.x - convert to trait.
    // Check if its a valid address.
    if (empty($location)) {
      return '';
    }

    $address = '';
    if (!empty($location['street'])) {
      $address .= $location['street'];
    }

    if (!empty($location['city'])) {
      if (!empty($location['street'])) {
        $address .= ', ';
      }

      $address .= $location['city'];
    }

    if (!empty($location['province'])) {
      if (!empty($location['street']) || !empty($location['city'])) {
        $address .= ', ';
      }

      // @@@ Fix this!
      if (substr($location['province'], 0, 3) == $location['country'] . '-') {
        $address .= substr($location['province'], 3);
        \Drupal::logger('Location')
          ->critical('BUG: Country found in province attribute.');
      }
      else {
        $address .= $location['province'];
      }
    }

    if (!empty($location['postal_code'])) {
      if (!empty($address)) {
        $address .= ' ';
      }
      $address .= $location['postal_code'];
    }

    if (!empty($location['country'])) {
      $address .= ', ' . $location['country'];
    }

    return $address;
  }

  /**
   * Geocoder list.
   */
  function getGeocoders() {
    // Implements location_get_general_geocoder_list()
    $list = &drupal_static(__FUNCTION__, []);

    if (!count($list)) {
      $files = file_scan_directory(drupal_get_path('module', 'dmaps') . '/geocoding', '/\.inc$/', ['nomask' => '/(\.\.?|CVS|\.svn)$/']);
      foreach ($files as $full_path_name => $fileinfo) {
        $list[] = $fileinfo->name;
      }
    }

    return $list;
  }

}
