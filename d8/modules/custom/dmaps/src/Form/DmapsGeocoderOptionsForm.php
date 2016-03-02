<?php

/**
 * @file
 * Contains \Drupal\dmaps\Form\DmapsGeocoderOptionsForm
 */

namespace Drupal\dmaps\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\dmaps\DmapsGeocoder;
use Drupal\dmaps\LocationCountriesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides form for managing specific geocoder settings.
 */
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

  /**
   * The dmaps LocationCountriesManager service
   *
   * @var \Drupal\dmaps\LocationCountriesManager
   */
  protected $countryManager;

  /**
   * The dmaps DmapsGeocoder service.
   *
   * @var \Drupal\dmaps\DmapsGeocoder
   */
  protected $geocoderManager;

  /**
   * Constructs a new DmapsGeocoderOptionsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *    The configuration factory.
   * @param \Drupal\dmaps\DmapsGeocoder $geocoder_manager
   *    The dmaps Geocoder service.
   * @param \Drupal\dmaps\LocationCountriesManager $country_manager
   *    The dmaps country manager service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, DmapsGeocoder $geocoder_manager, LocationCountriesManager $country_manager) {
    parent::__construct($config_factory);
    $request = $this->getRequest();
    $this->iso = $request->attributes->get('iso');
    $this->service = $request->attributes->get('service');
    $this->configFactory = $config_factory;
    $this->countryManager = $country_manager;
    $this->geocoderManager = $geocoder_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('dmaps.geocoder'),
      $container->get('dmaps.location_countries_manager')
    );
  }

  /**
   * @inheritdoc
   */
  public function getFormId() {
    return 'dmaps_geocoder_options';
  }

  /**
   * @inheritdoc
   */
  public function getEditableConfigNames() {
    return ['dmaps.geocoder_' . $this->iso . '_' . $this->service . '_settings'];
  }

  /**
   * @inheritdoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config_name = $this->getConfigName();
    $config = $this->configFactory->getEditable($config_name);
    $this->countryManager->locationLoadCountry($this->iso);

    // @todo Need to refactor when geocoder plugins will be implemented
    $geocode_settings_form_function_specific = 'location_geocode_' . $this->iso . '_' . $this->service . '_settings';
    $geocode_settings_form_function_general = $this->service . '_geocode_settings';
    if (function_exists($geocode_settings_form_function_specific)) {
      $form = $geocode_settings_form_function_specific($this->iso, $config);
      parent::buildForm($form, $form_state);
    }

    $this->geocoderManager->initGeocoder($this->service);

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

  /**
   * @inheritdoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config_name = $this->getConfigName();
    $config = $this->configFactory->getEditable($config_name);
    $values = $form_state->getValues();
    $exclude = ['submit', 'op', 'form_build_id', 'form_token', 'form_id'];
    foreach ($values as $key => $value) {
      if (!in_array($key, $exclude)) {
        $config->set($key, $value);
      }
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Build config name string from geocoder service name and country iso.
   *
   * @return string
   */
  protected function getConfigName() {
    return 'dmaps.geocoder.' . $this->service . '_' . $this->iso;
  }

}
