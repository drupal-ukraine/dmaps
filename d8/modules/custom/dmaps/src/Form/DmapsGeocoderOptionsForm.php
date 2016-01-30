<?php
/**
 * Contains \Drupal\dmaps\Form\DmapsGeocoderOptionsForm
 */

namespace Drupal\dmaps\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


class DmapsGeocoderOptionsForm extends ConfigFormBase {
  /**
   * Country iso
   *
   * @var mixed
   */
  protected $iso;
  /**
   * Geocoder provider name
   *
   * @var mixed
   */
  protected $service;

  public function __construct(\Drupal\Core\Config\ConfigFactoryInterface $config_factory) {
    $request = \Drupal::request();
    $this->iso = $request->attributes->get('iso');
    $this->service = $request->attributes->get('service');

    parent::__construct($config_factory);
  }

  public function getFormId() {
    return 'dmaps_geocoder_options';
  }

  public function getEditableConfigNames() {
    return ['dmaps.geocoder_' . $this->iso . '_' . $this->service . '_settings'];
  }

  /**
   * @inheritdoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dmaps.geocoder_' . $this->iso . '_' . $this->service . '_settings');
    $country_manager = \Drupal::service('dmaps.location_countries_manager');
    $country_manager->locationLoadCountry($this->iso);
    // @todo Need to refactor when geocoder plugins will be implemented
    $geocode_settings_form_function_specific = 'location_geocode_' . $this->iso . '_' . $this->service . '_settings';
    $geocode_settings_form_function_general = $this->service . '_geocode_settings';
    if (function_exists($geocode_settings_form_function_specific)) {
      $form = $geocode_settings_form_function_specific($this->iso, $config);
      parent::buildForm($form, $form_state);
    }

    $geocoder = \Drupal::service('dmaps.geocoder');
    $geocoder->initGeocoder($this->service);

    if (function_exists($geocode_settings_form_function_general)) {
      $form = $geocode_settings_form_function_general($this->iso, $config);
    }
    else {
      $form = [
        '#type' => 'markup',
        '#markup' => $this->t('No configuration parameters are necessary, or a form to take such paramters has not been implemented.'),
      ];
    }


    return parent::buildForm($form, $form_state);
  }

  /**
   * Route title callback
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|null|string
   */
  public function getPagetitle() {
    return $this->t('Configure parameters for %service geocoding', ['%service' => $this->service]);
  }


}