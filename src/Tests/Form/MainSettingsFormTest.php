<?php

/**
 * @file
 * Contains \Drupal\dmaps\Tests\Form\MainSettingsFormTest.
 */

namespace Drupal\dmaps\Tests\Form;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the MainSettingsFormTest class.
 *
 * @group Form
 */
class MainSettingsFormTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('dmaps');

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $web_user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($web_user);
  }

  /**
   * Testing Main settings form for Dmaps module.
   *
   * @throws \Exception
   *   Exceptions if tests failed.
   */
  function testSettingsForm() {
    $this->drupalGet('/admin/config/content/location');
    $this->assertText('Default country selection', 'Default country selection option is present');
    $this->drupalPostForm('/admin/config/content/location', [], t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'));
  }

}
