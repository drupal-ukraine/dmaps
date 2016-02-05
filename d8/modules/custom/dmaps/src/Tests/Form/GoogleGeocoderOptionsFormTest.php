<?php

/**
 * @file
 * Contains \Drupal\dmaps\Tests\Form\GoogleGeocoderOptionsFormTest.
 */

namespace Drupal\dmaps\Tests\Form;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the google geocoder options form.
 *
 * @group dmaps
 */
class GoogleGeocoderOptionsFormTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['dmaps'];

  /**
   * A user that has permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $web_user;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->web_user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($this->web_user);
  }

  /**
   * Testing Google Geocoder Options form for Dmaps module.
   *
   * @throws \Exception
   *   Exceptions if tests failed.
   */
  function testGoogleGeocoderOptionsForm() {
    $this->drupalGet('/admin/config/content/location/geocoding/af/google');
    $this->assertFieldByName('location_geocode_google_apikey', NULL, t('Google Geocoding API Server Key'));
    $this->assertFieldByName('location_geocode_google_delay', NULL, t('Delay between geocoding requests (is milliseconds)'));
    $this->assertFieldByName('location_geocode_af_google_accuracy_code', 3, t('Google Maps Geocoding Accuracy for af'));
    $edit = [
      'location_geocode_google_apikey' => 'AIzaSyCPekZ5UubHuL_kKJn3n0HN_63OyRsqXDs',
      'location_geocode_google_delay' => 500,
      'location_geocode_af_google_accuracy_code' => 3,
    ];
    $this->drupalPostForm('/admin/config/content/location/geocoding/af/google', $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'));
  }

}
