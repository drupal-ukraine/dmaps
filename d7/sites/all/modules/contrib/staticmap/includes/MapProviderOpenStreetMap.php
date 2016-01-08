<?php
/**
 * @file
 * OpenStreetMap provider.
 */

/**
 * Class MapProviderOpenStreetMap
 */
class MapProviderOpenStreetMap extends MapProviderBase {
  public $apiUri = 'http://ojw.dev.openstreetmap.org/StaticMap/?';

  /**
   * Parameters builder.
   *
   * @inhetirdoc
   */
  public function buildParams(&$items, &$settings) {
    $parameters = array(
      'show' => 1,
      'layer' => $settings['preset']['maptype'],
      'z' => !empty($settings['preset']['zoom']) ? $settings['preset']['zoom'] : 12,
      'size' => $settings['preset']['mapsize'],
    );

    foreach ($items as $key => $item) {
      // @TODO: This only works with lat/lon points
      $parts = explode(',', $item);
      $parameters['mlat' . $key] = $parts[0];
      $parameters['mlon' . $key] = $parts[1];

      if ($key == 0) {
        $parameters['lat'] = $parts[0];
        $parameters['lon'] = $parts[1];
      }
    }

    return $parameters;
  }

  /**
   * Preset altering.
   *
   * @inhetirdoc
   */
  public function presetFormAlter(&$form, &$form_state, $preset_data) {
    $elements = array();

    $elements['maptype'] = array(
      '#type' => 'select',
      '#title' => t('Map Type'),
      '#default_value' => (!empty($preset_data['maptype'])) ? $preset_data['maptype'] : '',
      '#options' => $this->mapTypes(),
    );
    $elements['zoom'] = array(
      '#type' => 'select',
      '#title' => t('Zoom Level'),
      '#default_value' => (!empty($preset_data['zoom'])) ? $preset_data['zoom'] : 12,
      '#options' => array('auto' => 'Auto') + drupal_map_assoc(range(0, 21)),
    );

    return $elements;
  }

  /**
   * Summary formatter settings.
   *
   * @inhetirdoc
   */
  public function fieldFormatterSettingsSummary($field, $instance, $view_mode, $preset_data) {
    $maptypes = $this->mapTypes();

    return t(
      'Map Style: @maptype',
      array(
        '@maptype' => !empty($maptypes[$preset_data['maptype']]) ? $maptypes[$preset_data['maptype']] : '',
      )
    );
  }

  /**
   * Map types data.
   *
   * @return array
   *   Map type names.
   */
  private function mapTypes() {
    return
      array(
        'mapnik' => t('Mapnik'),
        'osmarender' => t('OSMA Render'),
        'cycle' => t('Cycle'),
        'skiing' => t('Skiing'),
        'maplint' => t('Map Lint'),
        'cloudmade_1' => t('Cloudmade 1'),
        'cloudmade_2' => t('Cloudmade 2'),
        'cloudmade_999' => t('Cloudmade 999'),
        'cloudmade_5' => t('Cloudmade 5'),
        'cloudmade_998' => t('Cloudmade 998'),
      );
  }
}
