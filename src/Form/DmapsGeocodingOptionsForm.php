<?php

/**
 * @file
 * Contains \Drupal\dmaps\Form\DmapsGeocodingOptionsForm.
 */

namespace Drupal\dmaps\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


class DmapsGeocodingOptionsForm extends ConfigFormBase {

  /**
   * @inheritdoc
   */
  public function getFormId() {
    return 'dmaps_geocoding_options';
  }

  /**
   * @inheritdoc
   */
  public function getEditableConfigNames() {
    return ['dmaps.geocoding_settings'];
  }

  /**
   * @inheritdoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dmaps.geocoding_settings');

    $form['location_geocode_google_minimum_accuracy'] = [
      '#type' => 'select',
      '#title' => $this->t('Google Maps geocoding minimum accuracy'),
      '#options' => dmaps_google_geocode_accuracy_codes(),
      '#default_value' => $config->get('location_geocode_google_minimum_accuracy'),
      '#description' => $this->t('The Google Maps geocoding API returns results with a given accuracy. Any responses below this minimum accuracy will be ignored. See a !accuracy_values_link.',
        ['!accuracy_values_link' => '<a href="http://code.google.com/apis/maps/documentation/reference.html#GGeoAddressAccuracy">description of these values</a>']
      )
    ];

    $form['countries'] = [];

    return parent::buildForm($form, $form_state);
  }

  /**
   * @inheritdoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);
  }
}
