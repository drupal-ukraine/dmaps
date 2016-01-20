<?php

/**
 * @file
 * Contains \Drupal\dmaps\Form\DmapsUtilForm.
 */

namespace Drupal\dmaps\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides form for managing locations cache.
 */
class DmapsUtilForm extends FormBase {
  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * DmapsUtilForm constructor.
   */
  public function __construct() {
    $this->cache = \Drupal::cache('dmaps');
  }

  /**
   * @inheritdoc
   */
  public function getFormId() {
    return 'dmaps_util_settings';
  }

  /**
   * @inheritdoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['province_clear'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Clear province cache'),
      '#description' => $this->t('If you have modified location.xx.inc files, you will need to clear the province cache to get Location to recognize the modifications.'),
    ];

    $form['supported_countries_clear'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Clear supported country list'),
      '#description' => $this->t('If you have added support for a new country, you will need to clear the supported country list to get Location to recognize the modifications.'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['province_clear_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear province cache'),
    ];

    $form['actions']['supported_countries_clear_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear supported country list'),
      '#submit' => ['::clearCountryList'],
    ];

    return $form;
  }

  /**
   * @inheritdoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->cache->invalidate('location:provinces');
    drupal_set_message(t('Location province cache cleared.'));
  }

  /**
   * Submit callback for 'Clear supported country list' button
   */
  public function clearCountryList() {
    $this->cache->invalidate('location:supported-countries');
    drupal_set_message(t('Location supported country list cleared.'));
  }
}
