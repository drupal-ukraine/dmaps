<?php
/**
 * Contains \Drupal\dmaps\Tests\Form\UtilFormTest.
 */

namespace Drupal\dmaps\Tests\Form;

use Drupal\simpletest\WebTestBase;

/**
 * Test Dmaps utils form
 *
 * @group Form
 */
class UtilFormTest extends WebTestBase {

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
   * Testing Utils form for Dmaps module.
   */
  function testUtilsForm() {
    $this->drupalGet('/admin/config/content/location/util');
    $this->assertText(t('Clear province cache'));
    $this->assertText(t('If you have modified location.xx.inc files, you will need to clear the province cache to get Location to recognize the modifications.'));
    $this->assertText(t('Clear supported country list'));
    $this->assertText(t('If you have added support for a new country, you will need to clear the supported country list to get Location to recognize the modifications.'));
    $this->drupalPostForm('/admin/config/content/location/util', [], t('Clear province cache'));
    $this->assertText(t('Location province cache cleared.'));
    $this->drupalPostForm('/admin/config/content/location/util', [], t('Clear supported country list'));
    $this->assertText(t('Location supported country list cleared.'));
  }
}
