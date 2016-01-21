<?php

/**
 * @file
 * Contains \Drupal\dmaps\Form\DmapsSettingsForm.
 */

namespace Drupal\dmaps\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides form for managing module settings.
 */
class DmapsSettingsForm extends ConfigFormBase {

  /**
   * @inheritdoc
   */
  public function getFormId() {
    return 'dmaps_admin_settings';
  }

  /**
   * @inheritdoc
   */
  protected function getEditableConfigNames() {
    return ['dmaps.admin_settings'];
  }

  /**
   * @inheritdoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dmaps.admin_settings');

    /** @var \Drupal\dmaps\LocationCountriesManager $countries_manager */
    $countries_manager = \Drupal::service('dmaps.location_countries_manager');
    // Rebuild the list of supported countries.
    $countries_manager->rebuildSupportedList();

    $form['location_default_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Default country selection'),
      '#default_value' => $config->get('location_default_country'),
      '#options' => $countries_manager->getIso3166List(),
      '#description' => $this->t('This will be the country that is automatically selected when a location form is served for a new location.'),
    ];
    $form['location_display_location'] = [
      '#type' => 'radios',
      '#title' => t('Toggle location display'),
      '#default_value' => $config->get('location_display_location'),
      '#options' => [
        0 => $this->t('Disable the display of locations.'),
        1 => $this->t('Enable the display of locations.'),
      ],
      '#description' => $this->t('If you are interested in turning off locations and having a custom theme control their display, you may want to disable the display of locations so your theme can take that function.'),
    ];
    $form['location_use_province_abbreviation'] = [
      '#type' => 'radios',
      '#title' => $this->t('Province display'),
      '#default_value' => $config->get('location_use_province_abbreviation'),
      '#options' => [
        0 => $this->t('Display full province name.'),
        1 => $this->t('Display province/state code.'),
      ],
    ];
    //@todo Change path to gmap module settings in the placeholder :gmap_module_settings.
    $form['location_usegmap'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use a Google Map to set latitude and longitude'),
      '#default_value' => $config->get('location_usegmap'),
      '#description' => $this->t('If the gmap.module is installed and <a href=":enabled">enabled</a>, and this setting is also turned on, users that are allowed to manually enter latitude/longitude coordinates will be able to do so with an interactive Google Map. You should also make sure you have entered a <a href=":google_maps_api_key">Google Maps API key</a> into your <a href=":gmap_module_settings">gmap module settings</a>.', [
        ':enabled' => Url::fromRoute('system.modules_list')->toString(),
        ':google_maps_api_key' => 'http://www.google.com/apis/maps',
        ':gmap_module_settings' => Url::fromRoute('dmaps.settings')->toString(),
      ]),
    ];
    $form['location_locpick_macro'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location chooser macro'),
      '#size' => 50,
      '#maxlength' => 500,
      '#default_value' => $config->get('location_locpick_macro'),
      '#description' => $this->t('If you would like to change the macro used to generate the location chooser map, you can do so here. Note: Behaviors <em>locpick</em> and <em>collapsehack</em> are forced to be enabled and cannot be changed.'),
    ];
    $form['location_jit_geocoding'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable JIT geocoding'),
      '#default_value' => $config->get('location_jit_geocoding'),
      '#description' => $this->t('If you are going to be importing locations in bulk directly into the database, you may wish to enable JIT geocoding and load the locations with source set to 4 (LOCATION_LATLON_JIT_GEOCODING). The system will automatically geocode locations as they are loaded.'),
    ];
    $form['maplink_external'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Map link'),
    ];
    $form['maplink_external']['location_maplink_external'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Open map link in new window'),
      '#default_value' => $config->get('maplink_external.location_maplink_external'),
      '#description' => $this->t('Select this if you want the map link to open in a separate window.'),
    ];
    // @todo add states, and enable this option if "Open map link in new window" is checked.
    $form['maplink_external']['location_maplink_external_method'] = [
      '#type' => 'radios',
      '#title' => $this->t('Open in new window method'),
      '#options' => [
        'target="_blank"' => 'target="_blank"',
        'rel="external"' => 'rel="external"',
      ],
      '#default_value' => $config->get('maplink_external.location_maplink_external_method'),
      '#description' => $this->t('If you have selected to open map in a new window this controls the method used to open in a new window.  target="_blank" will just work but is not XTHML Strict compliant.  rel="external" is XHTML Strict compliant but will not open in a new window unless you add some jQuery to your site to add the target attribute. If you are unsure leave set to target="_blank".'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * @inheritdoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dmaps.admin_settings')
      ->set('location_default_country', $form_state->getValue('location_default_country'))
      ->set('location_display_location', $form_state->getValue('location_display_location'))
      ->set('location_use_province_abbreviation', $form_state->getValue('location_use_province_abbreviation'))
      ->set('location_usegmap', $form_state->getValue('location_usegmap'))
      ->set('location_locpick_macro', $form_state->getValue('location_locpick_macro'))
      ->set('location_jit_geocoding', $form_state->getValue('location_jit_geocoding'))
      ->set('maplink_external.location_maplink_external', $form_state->getValue('location_maplink_external'))
      ->set('maplink_external.location_maplink_external_method', $form_state->getValue('location_maplink_external_method'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
