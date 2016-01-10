<?php

/**
 * @file
 * Contains \Drupal\dmaps\LocationCountriesManagerInterface.
 */

namespace Drupal\dmaps;

/**
 * Defines a common interface for location country managers.
 */
interface LocationCountriesManagerInterface {

  /**
   * Returns an associative array of countries currently supported by the
   * location system where.
   *
   * -> the keys represent the two-letter ISO code and
   * -> the values represent the English name of the country.
   * The array is sorted the index (i.e., by the short English name
   * of the country).
   *
   * Please note the different between "supported" countries and "configured"
   * countries: A country being "supported" means that there is an include file
   * to support the country while "configure" implies that the site admin has
   * configured the site to actually use that country.
   *
   * @return array
   *   An array of country code => country name pairs.
   */
  public function getSupportedList();

}
