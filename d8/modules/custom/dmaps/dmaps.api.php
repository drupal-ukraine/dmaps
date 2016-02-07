<?php

/**
 * @file
 * Describes hooks and plugins provided by the Dmaps module.
 */

/**
 * Alter the Dmaps Country provinces list for a country.
 *
 * @param array $provinces
 *   List of provinces supported by module.
 * @param string $country_code
 *   Country code.
 */
function hook_dmaps_location_provinces_alter(array &$provinces, $country_code) {
  if ($country_code == 'us') {
    $provinces = [
      'AL' => t('Alabama'),
      'AK' => t('Alaska'),
      'AZ' => t('Arizona'),
      'AR' => t('Arkansas'),
      // ...
    ];
  }
}

/**
 * Alter the Dmaps Country provinces list for a country.
 *
 * @param array $provinces
 *   List of provinces for a country supported by module.
 */
function hook_dmaps_location_provinces_COUNTRY_CODE_alter(array &$provinces) {
  $provinces = [
    'AL' => t('Alabama'),
    'AK' => t('Alaska'),
    'AZ' => t('Arizona'),
    'AR' => t('Arkansas'),
    // ...
  ];
}
