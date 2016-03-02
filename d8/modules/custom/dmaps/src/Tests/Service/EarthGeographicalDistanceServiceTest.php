<?php

/**
 * @file
 * Contains \Drupal\dmaps\Tests\Service\EarthGeographicalDistanceServiceTest.
 */

namespace Drupal\dmaps\Tests\Service;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the EarthGeographicalDistances service.
 *
 * @group dmaps
 */
class EarthGeographicalDistanceServiceTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['dmaps'];

  /**
   * Test XYZ coordinates calculation.
   */
  public function testGeographicalCoordinates() {
    $distance_service = \Drupal::service('dmaps.earth_service');

    $expected = [5076436.1926031, 3086400.2318368, 2312685.5571307];
    $result = $distance_service->getXYZ(31.299, 21.4);
    $this->assertArrayEpsilon($result, $expected, 0.01);

    // Taj Mahal
    $expected = [1179389.7524227, 605469.92806515, 6217918.5984722];
    $result = $distance_service->getXYZ(27.174858, 78.042383);
    $this->assertArrayEpsilon($result, $expected, 0.01, 'Taj Mahal');

    // Four Corners
    $expected = [-1667195.89356, -1256280.4293852, -6006637.16009];
    $result = $distance_service->getXYZ(36.999084, -109.045223);
    $this->assertArrayEpsilon($result, $expected, 0.01, 'Four Corners');

    // North Magnetic Pole
    $expected = [-335727.75631839, -2620765.1318567, -5785664.2896111];
    $result = $distance_service->getXYZ(82.7, -114.4);
    $this->assertArrayEpsilon($result, $expected, 0.01, 'North Magnetic Pole');

    // Wall Drug
    $expected = [-976074.77491191, -942362.77881868, -6211268.2459008];
    $result = $distance_service->getXYZ(43.993266, -102.241794);
    $this->assertArrayEpsilon($result, $expected, 0.01, 'Wall Drug');
  }

  /**
   * Custom assertion -- will check each element of an array against a reference value.
   */
  protected function assertArrayEpsilon($result, $expected, $epsilon, $message = '', $group = 'Other') {
    foreach ($expected as $k => $test) {
      $lower = $test - $epsilon;
      $upper = $test + $epsilon;
      if ($result[$k] < $lower || $result[$k] > $upper) {
        if (!$message) {
          $amt = abs($test - $result[$k]);
          $message = t('Value deviates by @amt, which is more than @maxdev.', [
            '@amt' => $amt,
            '@maxdev' => $epsilon,
          ]);
        }

        $this->assert('fail', $message, $group);
      }
      else {
        if (!$message) {
          $message = t('Value within expected margin.');
        }
        $this->assert('pass', $message, $group);
      }
    }
  }
}
