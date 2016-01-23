<?php

/**
 * @file
 * Contains \Drupal\dmaps\Tests\Form\LocationUtilitiesFormTest.
 */

namespace Drupal\dmaps\Tests\Form;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the location utilities form.
 *
 * @group dmaps
 */
class LocationUtilitiesFormTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('dmaps');

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
  function testClearProvincesButton() {
    $this->drupalGet('/admin/config/content/location/util');
    $this->assertText(t('Location province cache cleared.'));
  }

  function testClearCountriesButton() {
    $this->drupalGet('/admin/config/content/location/util');
    $this->assertText(t('Location supported country list cleared.'));
  }

}
