<?php

/**
 * @file
 * Contains \Drupal\dmaps\Tests\Form\MainSettingsFormTest.
 */

namespace Drupal\dmaps\Tests\Form;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the main settings form.
 *
 * @group dmaps
 */
class MainSettingsFormTest extends WebTestBase {

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
   * Testing Main settings form for Dmaps module.
   *
   * @throws \Exception
   *   Exceptions if tests failed.
   */
  function testSettingsForm() {
    $this->drupalGet('/admin/config/content/location');
    $this->assertFieldByName('location_default_country', NULL, 'The field "Default country selection" is available.');
    $this->assertFieldByName('location_display_location', NULL, 'The field "Toggle location display" is available.');
    $this->assertFieldByName('location_use_province_abbreviation', NULL, 'The field "Province display" is available.');
    // @todo add validation for field location_usegmap, it should be disabled if module gmap is not installed.
    $this->assertFieldByName('location_usegmap', NULL, 'The field "Use a Google Map to set latitude and longitude" is available.');
    $this->assertFieldByName('location_locpick_macro', NULL, 'The field "Location chooser macro" is available.');
    $this->assertFieldByName('location_jit_geocoding', NULL, 'The field "Enable JIT geocoding" is available.');
    $this->assertFieldByName('location_maplink_external', NULL, 'The field "Open map link in new window" is available.');
    $this->assertFieldByName('location_maplink_external_method', NULL, 'The field "Open in new window method" is available.');
    $this->drupalPostForm('/admin/config/content/location', [], t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'));
  }

}
