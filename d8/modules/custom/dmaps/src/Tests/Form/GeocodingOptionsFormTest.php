<?php

/**
 * @file
 * Contains \Drupal\dmaps\Tests\Form\GeocodingOptionsFormTest.
 */

namespace Drupal\dmaps\Tests\Form;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the geocoding options form.
 *
 * @group dmaps
 */
class GeocodingOptionsFormTest extends WebTestBase {

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
   * Testing Geocoding Options form for Dmaps module.
   *
   * @throws \Exception
   *   Exceptions if tests failed.
   */
  function testFormPage() {
    $this->drupalGet('/admin/config/content/location/geocoding');
    $edit = ['countries[af][geocode_af]' => 'google'];
    $this->drupalPostForm('/admin/config/content/location/geocoding', $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'));
    $this->assertLink(t('Configure parameters'));
  }

}
