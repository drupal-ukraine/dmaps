<?php

/**
 * @file
 * Contains \Drupal\dmaps\Form\DmapsGeocodingOptionsForm.
 */

namespace Drupal\dmaps\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

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
    // @todo 8.x-2.x - need to rewrite after implementing geocoding providers plugins.
    $config = $this->config('dmaps.geocoding_settings');
    $geocoder = \Drupal::service('dmaps.geocoder');
    $country_manager = \Drupal::service('dmaps.location_countries_manager');

    $form['countries'] = [
      '#type' => 'table',
      '#title' => $this->t('Geocoding Options'),
      '#header' => [
        $this->t('Country'),
        $this->t('Options'),
        $this->t('Configure'),
      ],
    ];

    // First, we build two arrays to help us figure out on the fly whether a specific country is covered by a multi-country geocoder,
    // and what the details of the multi-country geocoder are
    // (1) Get list of geocoders.
    $general_geocoders_list = $geocoder->getGeocoders();
    // (2) get data about each geocoder and the list of countries covered by each geocoder.
    $general_geocoders_data = [];
    $general_geocoders_countries = [];
    foreach ($general_geocoders_list as $geocoder_name) {
      $geocoder->initGeocoder($geocoder_name);
      $info_function = $geocoder_name . '_geocode_info';
      if (function_exists($info_function)) {
        $general_geocoders_data[$geocoder_name] = $info_function();
      }

      $countries_function = $geocoder_name . '_geocode_country_list';
      if (function_exists($countries_function)) {
        $general_geocoders_countries[$geocoder_name] = $countries_function();
      }
    }

    $supported_countries = $country_manager->getSupportedList();
    foreach ($supported_countries as $country_iso => $country_name) {
      $geocoding_options = [];
      $country_manager->locationLoadCountry($country_iso);

      $form['countries'][$country_iso] = [
        '#type' => 'container',
      ];

      $country_element_key = 'label_' . $country_iso;
      $form['countries'][$country_iso][$country_element_key] = [
        '#type' => 'container',
        '#attributes' => [
          'id' => [$country_iso],
        ],
      ];
      $form['countries'][$country_iso][$country_element_key]['country'] = [
        '#markup' => $country_name,
      ];

      // Next, we look for options presented by country specific providers.
      $country_specific_provider_function = 'location_geocode_' . $country_iso . '_providers';
      if (function_exists($country_specific_provider_function)) {
        foreach ($country_specific_provider_function() as $name => $details) {
          $geocoder_link = '<a href="' . $details['url'] . '">' . $details['name'] . '</a>';
          $terms_link = '(<a href="' . $details['tos'] . '">' . $this->t('Terms of Use') . '</a>)';
          $geocoding_options[$name . '|' . $country_iso] = $geocoder_link . ' ' . $terms_link;
        }
      }

      foreach ($general_geocoders_list as $geocoder_name) {
        if (in_array($country_iso, $general_geocoders_countries[$geocoder_name])) {
          $geocoder_link = '<a href="' . $general_geocoders_data[$geocoder_name]['url'] . '">' . $general_geocoders_data[$geocoder_name]['name'] . '</a>';
          $terms_link = '(<a href="' . $general_geocoders_data[$geocoder_name]['tos'] . '">' . $this->t('Terms of Use') . '</a>)';
          $geocoding_options[$geocoder_name] = $geocoder_link . ' ' . $terms_link;
        }
      }

      $country_geocode_key = 'location_geocode_' . $country_iso;
      if (count($geocoding_options)) {
        $geocoding_options = array_merge(['none' => $this->t('None')], $geocoding_options);

        $form['countries'][$country_iso][$country_geocode_key] = [
          '#type' => 'radios',
          '#default_value' => $config->get($country_geocode_key) ? $config->get($country_geocode_key) : 'none',
          '#options' => $geocoding_options,
        ];
      }
      else {
        $form['countries'][$country_iso][$country_geocode_key] = [
          '#type' => 'markup',
          '#markup' => $this->t('None supported.'),
        ];
      }

      $config_link_key = 'location_geocode_config_link_' . $country_iso;
      $current_value = $config->get($country_geocode_key);
      if ($current_value == 'none') {
        $form['countries'][$country_iso][$config_link_key] = [
          '#type' => 'markup',
          '#markup' => $this->t('No service selected for country.'),
        ];
      }
      else {
        $current_val_chopped = substr($current_value, 0, strpos($current_value, '|'));
        $geocode_settings_form_function_specific = $country_geocode_key . '_' . $current_val_chopped . '_settings';
        $geocode_settings_form_function_general = $current_value . '_geocode_settings';

        if (function_exists($geocode_settings_form_function_specific)) {
          $form['countries'][$country_iso][$config_link_key] = [
            '#type' => 'link',
            '#title' => $this->t('Configure parameters'),
            '#url' => Url::fromRoute('dmaps.locations.geocoder_options', [
              'iso' => $country_iso,
              'service' => $current_val_chopped,
            ])->toString(),
          ];
        }
        elseif (function_exists($geocode_settings_form_function_general)) {
          $form['countries'][$country_iso][$config_link_key] = [
            '#type' => 'link',
            '#title' => $this->t('Configure parameters'),
            '#url' => Url::fromRoute('dmaps.locations.geocoder_options', [
              'iso' => $country_iso,
              'service' => $current_value,
            ])->toString(),
          ];
        }
        else {
          $form['countries'][$country_iso][$config_link_key] = [
            '#type' => 'markup',
            '#markup' => $this->t('No configuration necessary for selected service.'),
          ];
        }
      }
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * @inheritdoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('dmaps.geocoding_settings');
    $geocoders = \Drupal::service('dmaps.geocoder')->getGeocoders();
    $geocoders_in_use = [];

    $values = $form_state->getValues();
    foreach ($values['countries'] as $country) {
      $key = key($country);
      $value = $country[$key];
      if (in_array($value, $geocoders)) {
        $geocoders_in_use[$value] = $value;
        $config->set($key, $country[$key]);
      }
    }
    $config->save();

    \Drupal::state()
      ->set('location_general_geocoders_in_use', $geocoders_in_use);

    parent::submitForm($form, $form_state);
  }
}
