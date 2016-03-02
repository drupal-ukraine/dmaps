<?php

/**
 * @file
 * Contains Drupal\dmaps\EarthGeographicalDistances.
 */

namespace Drupal\dmaps;

class EarthGeographicalDistances {

  /**
   *  Radius of Earth semimajor.
   */
  const RADIUS_SEMIMAJOR = 6378137.0;

  /**
   * Earth flattening.
   *
   * @var float
   */
  protected $flattening = 1 / 298.257223563;

  /**
   * Radius of Earth semiminor.
   *
   * @var float
   */
  protected $radius_semiminor;

  /**
   * The second flattening of the ellipsoid.
   *
   * @var int
   */
  protected $eccentricity_sq;

  /**
   * EarthService constructor.
   */
  public function __construct() {
    $this->radius_semiminor = (self::RADIUS_SEMIMAJOR * (1 - $this->flattening));
    $this->eccentricity_sq = (2 * $this->flattening - pow($this->flattening, 2));
  }

  /**
   * Estimate the Earth's radius at a given latitude.
   * Default to an approximate average radius for the United States.
   *
   * @param float $latitude
   *   Latitude value.
   *
   * @return float
   *   Radius for given latitude.
   */
  protected function getEarthRadius($latitude = 37.9) {
    $lat = deg2rad($latitude);

    $x = cos($lat) / self::RADIUS_SEMIMAJOR;
    $y = sin($lat) / $this->radius_semiminor;

    return 1 / (sqrt($x * $x + $y * $y));
  }

  /**
   * Convert longitude and latitude to earth-centered earth-fixed coordinates.
   * X axis is 0 long, 0 lat; Y axis is 90 deg E; Z axis is north pole.
   *
   * @param $longitude
   *   Longitude value.
   * @param $latitude
   *   Latitude value.
   * @param int $altitude
   *   Altitude value (Height).
   *
   * @return array
   *   Array of coordinates.
   */
  public function getXYZ($longitude, $latitude, $altitude = 0) {
    $long = deg2rad($longitude);
    $lat = deg2rad($latitude);

    $coslong = cos($long);
    $coslat = cos($lat);
    $sinlong = sin($long);
    $sinlat = sin($lat);
    $radius = self::RADIUS_SEMIMAJOR / sqrt(1 - $this->eccentricity_sq * $sinlat * $sinlat);
    $x = ($radius + $altitude) * $coslat * $coslong;
    $y = ($radius + $altitude) * $coslat * $sinlong;
    $z = ($radius * (1 - $this->eccentricity_sq) + $altitude) * $sinlat;

    return [$x, $y, $z];
  }

  /**
   * Convert a given angle to earth-surface distance.
   *
   * @param $angle
   *   Angle value.
   * @param float $latitude
   *   Latitude value.
   *
   * @return float
   *   Distance value.
   */
  protected function getArclength($angle, $latitude = 37.9) {
    return deg2rad($angle) * $this->getEarthRadius($latitude);
  }

  /**
   * Estimate the earth-surface distance between two locations.
   *
   * @param $longitude1
   *   Longitude value of first location.
   * @param $latitude1
   *   Latitude value of first location.
   * @param $longitude2
   *   Longitude value of second location.
   * @param $latitude2
   *   Latitude value of second location.
   *
   * @return float
   *   Distance value.
   */
  public function getDistance($longitude1, $latitude1, $longitude2, $latitude2) {
    $long1 = deg2rad($longitude1);
    $lat1 = deg2rad($latitude1);
    $long2 = deg2rad($longitude2);
    $lat2 = deg2rad($latitude2);
    $radius = $this->getEarthRadius(($latitude1 + $latitude2) / 2);

    $cosangle = cos($lat1) * cos($lat2) * (cos($long1) * cos($long2) + sin($long1) * sin($long2)) + sin($lat1) * sin($lat2);

    return acos($cosangle) * $radius;
  }

  /**
   * Returns the SQL fragment needed to add a column called 'distance' to a query that includes the location table.
   *
   * @param $longitude
   *   The measurement point.
   * @param $latitude
   *   The measurement point.
   * @param string $tbl_alias
   *   If necessary, the alias name of the location table to work from.  Only required when working with named {location} tables.
   *
   * @return string
   *   SQL fragment.
   */
  public function getDistanceSql($longitude, $latitude, $tbl_alias = '') {
    // Make a SQL expression that estimates the distance to the given location.
    $long = deg2rad($longitude);
    $lat = deg2rad($latitude);
    $radius = $this->getEarthRadius($latitude);

    // If the table alias is specified, add on the separator.
    $tbl_alias = empty($tbl_alias) ? $tbl_alias : ($tbl_alias . '.');

    $coslong = cos($long);
    $coslat = cos($lat);
    $sinlong = sin($long);
    $sinlat = sin($lat);

    return "(COALESCE(ACOS($coslat*COS(RADIANS({$tbl_alias}latitude))*($coslong*COS(RADIANS({$tbl_alias}longitude)) + $sinlong*SIN(RADIANS({$tbl_alias}longitude))) + $sinlat*SIN(RADIANS({$tbl_alias}latitude))), 0.00000)*$radius)";
  }

  /**
   * Estimate the min and max longitudes within $distance of a given location.
   *
   * @param $longitude
   *   Longitude value.
   * @param $latitude
   *   Latitude value.
   * @param $distance
   *   Distance value.
   *
   * @return array
   *   Array with min and max longitudes.
   */
  public function getLongitudeRange($longitude, $latitude, $distance) {
    $long = deg2rad($longitude);
    $lat = deg2rad($latitude);
    $radius = $this->getEarthRadius($latitude) * cos($lat);

    $angle = pi();
    if ($radius > 0) {
      $angle = abs($distance / $radius);
      $angle = min($angle, pi());
    }

    $minlong = $long - $angle;
    $maxlong = $long + $angle;
    if ($minlong < -pi()) {
      $minlong = $minlong + pi() * 2;
    }
    if ($maxlong > pi()) {
      $maxlong = $maxlong - pi() * 2;
    }

    return [rad2deg($minlong), rad2deg($maxlong)];
  }

  /**
   * Estimate the min and max latitudes within $distance of a given location.
   *
   * @param $longitude
   *   Longitude value.
   * @param $latitude
   *   Latitude value.
   * @param $distance
   *   Distance value.
   *
   * @return array
   *   Array with min and max latitudes.
   */
  public function getLatitudeRange($longitude, $latitude, $distance) {
    $long = deg2rad($longitude);
    $lat = deg2rad($latitude);
    $radius = $this->getEarthRadius($latitude);

    $angle = $distance / $radius;
    $minlat = $lat - $angle;
    $maxlat = $lat + $angle;
    $rightangle = pi() / 2;
    // Wrapped around the south pole.
    if ($minlat < -$rightangle) {
      $overshoot = -$minlat - $rightangle;
      $minlat = -$rightangle + $overshoot;
      if ($minlat > $maxlat) {
        $maxlat = $minlat;
      }
      $minlat = -$rightangle;
    }
    // Wrapped around the north pole.
    if ($maxlat > $rightangle) {
      $overshoot = $maxlat - $rightangle;
      $maxlat = $rightangle - $overshoot;
      if ($maxlat < $minlat) {
        $minlat = $maxlat;
      }
      $maxlat = $rightangle;
    }

    return [rad2deg($minlat), rad2deg($maxlat)];
  }
}
