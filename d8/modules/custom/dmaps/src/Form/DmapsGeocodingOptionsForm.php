<?php

/**
 * @file
 * Contains \Drupal\dmaps\Form\DmapsGeocodingOptionsForm.
 */

namespace Drupal\dmaps\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dmaps\DmapsGeocoder;
use Drupal\dmaps\LocationCountriesManager;

class DmapsGeocodingOptionsForm extends ConfigFormBase {

  /**
   * Geocoder provider
   *
   * @var string
   */
  protected $geocoderManager;

  /**
   * The dmaps LocationCountriesManager service
   *
   * @var \Drupal\dmaps\LocationCountriesManager
   */
  protected $countryManager;

  /**
   * Constructs a new DmapsGeocodingOptionsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\dmaps\DmapsGeocoder $geocoder_manager
   *   The dmaps Geocoder service.
   * @param \Drupal\dmaps\LocationCountriesManager $country_manager
   *   The dmaps country manager service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, DmapsGeocoder $geocoder_manager, LocationCountriesManager $country_manager) {
    parent::__construct($config_factory);
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
    // @todo 8.x-2.x - need to refactor after implementing geocoding providers plugins.
    $config = $this->config('dmaps.geocoding_settings');

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
    $geocoders_list = $this->geocoderManager->getGeocoders();
    // (2) get data about each geocoder and the list of countries covered by each geocoder.
    $geocoders_data = [];
    $geocoders_countries = [];
    foreach ($geocoders_list as $geocoder_name) {
      $this->geocoderManager->initGeocoder($geocoder_name);
      $info_function = $geocoder_name . '_geocode_info';
      if (function_exists($info_function)) {
        $geocoders_data[$geocoder_name] = $info_function();
      }

      $countries_function = $geocoder_name . '_geocode_country_list';
      if (function_exists($countries_function)) {
        $geocoders_countries[$geocoder_name] = $countries_function();
      }
    }

    $supported_countries = $this->countryManager->getSupportedList();
    foreach ($supported_countries as $country_iso => $country_name) {
      $geocoding_options = [];
      $this->countryManager->locationLoadCountry($country_iso);

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
          $url = Url::fromUri($details['url']);
          $geocoder_link = Link::fromTextAndUrl($details['name'], $url)
            ->toRenderable();
          $geocoder_link = \Drupal::service('renderer')->render($geocoder_link);
          $url = Url::fromUri($details['tos']);
          $terms_link = Link::fromTextAndUrl($this->t('Terms of Use'), $url)
            ->toRenderable();
          $terms_link = \Drupal::service('renderer')->render($terms_link);
          $geocoding_options[$name . '|' . $country_iso] = $geocoder_link . ' ' . $terms_link;
        }
      }

      foreach ($geocoders_list as $geocoder_name) {
        if (in_array($country_iso, $geocoders_countries[$geocoder_name])) {
          $url = Url::fromUri($geocoders_data[$geocoder_name]['url']);
          $geocoder_link = Link::fromTextAndUrl($geocoders_data[$geocoder_name]['name'], $url)
            ->toRenderable();
          $url = Url::fromUri($geocoders_data[$geocoder_name]['tos']);
          $terms_link = Link::fromTextAndUrl($this->t('Terms of Use'), $url)
            ->toRenderable();
          $renderer = \Drupal::service('renderer');
          $terms_link = $renderer->render($terms_link);
          $terms_link = '(' . $terms_link . ')';
          $geocoding_options[$geocoder_name] = $renderer->render($geocoder_link) . ' ' . $terms_link;
        }
      }

      $country_geocode_key = 'geocode_' . $country_iso;
      $current_value = $config->get($country_iso);
      $current_value = isset($current_value['geocoder']) ? $current_value['geocoder'] : 'none';

      if (count($geocoding_options)) {
        $geocoding_options = array_merge(['none' => $this->t('None')], $geocoding_options);

        $form['countries'][$country_iso][$country_geocode_key] = [
          '#type' => 'radios',
          '#default_value' => $current_value ? $current_value : 'none',
          '#options' => $geocoding_options,
        ];
      }
      else {
        $form['countries'][$country_iso][$country_geocode_key] = [
          '#type' => 'markup',
          '#markup' => $this->t('None supported.'),
        ];
      }

      $config_link_key = 'config_link_' . $country_iso;
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
            ]),
          ];
        }
        elseif (function_exists($geocode_settings_form_function_general)) {
          $form['countries'][$country_iso][$config_link_key] = [
            '#type' => 'link',
            '#title' => $this->t('Configure parameters'),
            '#url' => Url::fromRoute('dmaps.locations.geocoder_options', [
              'iso' => $country_iso,
              'service' => $current_value,
            ]),
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
    $geocoders_in_use = [];
    $config = $this->config('dmaps.geocoding_settings');

    $values = $form_state->getValues();
    foreach ($values['countries'] as $iso => $country) {
      $key = key($country);
      $value = $country[$key];
      $geocoders_in_use[$value] = $value;
//      $config->set($iso, $value);
      $config->set($iso, array('country' => $iso, 'geocoder' => $value));
    }
    $config->save();

    \Drupal::state()
      ->set('location_general_geocoders_in_use', $geocoders_in_use);

    parent::submitForm($form, $form_state);
  }
}
