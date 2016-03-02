<?php

/**
 * @file
 * Builds placeholder replacement tokens for locations-related data.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Component\Utility\Html;

/**
 * Implements hook_token_info().
 */
function dmaps_token_info() {
  $type = [
    'name' => t('Location', [], ['context' => 'geolocation']),
    'description' => t('Tokens related to location.'),
    'needs-data' => 'location',
  ];

  // Core tokens for location.
  $location = [];
  $location['name'] = [
    'name' => t('Location Name'),
    'description' => t('Location Name (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['street'] = [
    'name' => t('Street'),
    'description' => t('Street (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['additional'] = [
    'name' => t('Additional'),
    'description' => t('Additional (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['city'] = [
    'name' => t('City'),
    'description' => t('City (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['province'] = [
    'name' => t('State/Province'),
    'description' => t('State/Province (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['province_name'] = [
    'name' => t('State/Province Name'),
    'description' => t('State/Province Name (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['postal_code'] = [
    'name' => t('Postal Code'),
    'description' => t('Postal Code (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['latitude'] = [
    'name' => t('Latitude'),
    'description' => t('Latitude (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['longitude'] = [
    'name' => t('Longitude'),
    'description' => t('Longitude (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['country'] = [
    'name' => t('Country'),
    'description' => t('Country (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];
  $location['country_name'] = [
    'name' => t('Country Name'),
    'description' => t('Country Name (If there are multiple locations, N is the iteration, starting with 0).'),
    'dynamic' => TRUE,
  ];

  $node = [
    'location' => [
      'name' => t('Location', [], ['context' => 'geolocation']),
      'description' => t('The location for this node.'),
      'type' => 'location',
    ],
  ];
  $user = [
    'location' => [
      'name' => t('Location', [], ['context' => 'geolocation']),
      'description' => t('The location for this user.'),
      'type' => 'location',
    ],
  ];

  return [
    'types' => ['location' => $type],
    'tokens' => [
      'location' => $location,
      'user' => $user,
      'node' => $node,
    ],
  ];
}

/**
 * Implements hook_tokens().
 */
function dmaps_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];
  $token_service = \Drupal::token();

  switch ($type) {
    case 'node':
      if ($location_tokens = $token_service->findWithPrefix($tokens, 'location')) {
        $locations = isset($data['node']->locations) ? $data['node']->locations : [];
        $replacements += $token_service->generate('location', $location_tokens, ['location' => $locations], $options, $bubbleable_metadata);
      }
      break;

    case 'user':
      if ($location_tokens = $token_service->findWithPrefix($tokens, 'location')) {
        $locations = isset($data['user']->locations) ? $data['user']->locations : [];
        $replacements += $token_service->generate('location', $location_tokens, ['location' => $locations], $options, $bubbleable_metadata);
      }
      break;

    case 'location':
      if (isset($data['location'])) {
        $locations = $data['location'];

        // Make sure that this is an array of locations and not a single location.
        if (isset($locations['lid'])) {
          $locations = [$locations];
        }

        // Handle all tokens that are dynamic. For example, location:name:1. This does
        // not handle location:name. That is handled lower.
        $tokens_helper = array(
          'name' => $token_service->findWithPrefix($tokens, 'name'),
          'street' => $token_service->findWithPrefix($tokens, 'street'),
          'additional' => $token_service->findWithPrefix($tokens, 'additional'),
          'city' => $token_service->findWithPrefix($tokens, 'city'),
          'province' => $token_service->findWithPrefix($tokens, 'province'),
          'province_name' => $token_service->findWithPrefix($tokens, 'province_name'),
          'postal_code' => $token_service->findWithPrefix($tokens, 'postal_code'),
          'latitude' => $token_service->findWithPrefix($tokens, 'latitude'),
          'longitude' => $token_service->findWithPrefix($tokens, 'longitude'),
          'country' => $token_service->findWithPrefix($tokens, 'country'),
          'country_name' => $token_service->findWithPrefix($tokens, 'country_name'),
        );

        foreach ($tokens_helper as $key => $tokens_find) {
          foreach ($tokens_find as $val => $original) {
            $replacements[$original] = _dmaps_locations_token_helper($locations, $key, $val, $options);
          }
        }

        // Handle values that do not have a specific location on them. For example,
        // handle location:city but not location:city:0 as it is handled above.
        foreach ($tokens as $key => $original) {
          if (isset($tokens_helper[$key])) {
            $replacements[$original] = _dmaps_locations_token_helper($locations, $key, 0, $options);
          }
        }
      }
      break;
  }

  return $replacements;
}

/**
 * Callback function to handle getting the correct location token.
 *
 * @param array $locations
 *   Array of locations.
 * @param string $location_key
 *   Location key. Ex.: 'street', 'city', 'country_name', etc.
 * @param int $which
 * @param array $options
 *   Array of token options.
 * @return string
 */
function _dmaps_locations_token_helper($locations, $location_key, $which = 0, array $options = []) {
  $country_manager = \Drupal::service('dmaps.location_countries_manager');
  $sanitize = !empty($options['sanitize']);

  // That location doesn't exist.
  if (!isset($locations[$which])) {
    return '';
  }

  switch ($location_key) {
    case 'country_name':
      // Load the country name and return it.
      if (!empty($locations[$which]['country'])) {
        $country = $locations[$which]['country'];
        $val = $country_manager->locationCountryName($country);

        return $sanitize ? Html::escape($val) : $val;
      }

      return '';

    case 'province_name':
      // Load the province name and return it.
      if (!empty($locations[$which]['country']) && !empty($locations[$which]['province'])) {
        $country = $locations[$which]['country'];
        $province = $locations[$which]['province'];
        $val = $country_manager->locationProvinceName($country, $province);
        return $sanitize ? Html::escape($val) : $val;
      }

      return '';
  }

  // Handle all other values.
  if (!empty($locations[$which][$location_key])) {
    return $sanitize ? Html::escape($locations[$which][$location_key]) : $locations[$which][$location_key];
  }

  return '';
}
