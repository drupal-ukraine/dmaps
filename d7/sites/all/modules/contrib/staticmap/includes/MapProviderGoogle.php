<?php
/**
 * @file
 * Google provider.
 */

/**
 * Class MapProviderGoogle
 */
class MapProviderGoogle extends MapProviderBase {
  public $apiUri = 'http://maps.google.com/maps/api/staticmap?';

  /**
   * Parameters builder.
   *
   * @inhetirdoc
   */
  public function buildParams(&$items, &$settings) {
    $parameters = array(
      'size' => $settings['preset']['mapsize'],
      'maptype' => $settings['preset']['maptype'],
      'markers' => implode('|', $items),
      'sensor' => 'false',
    );
    if (count($items) < 2) {
      $parameters['zoom'] = $settings['preset']['zoom'];
    }

    $premier_id = variable_get('staticmap_google_premier', '');
    if ($premier_id) {
      $parameters['client'] = $premier_id;
    }

    $api_key = variable_get('staticmap_google_api_key', '');
    if ($api_key) {
      $parameters['key'] = $api_key;
    }

    return $parameters;
  }

  /**
   * Preset form altering.
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
      '#title' => t('Zoom'),
      '#options' => array('auto' => t('Automatic')) + drupal_map_assoc(range(0, 21)),
      '#default_value' => isset($preset_data['zoom']) ? $preset_data['zoom'] : 'auto',
    );
    return $elements;
  }

  /**
   * Summary settings for field formatter.
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
   * List of map types.
   *
   * @return array
   *   Array with map type names.
   */
  private function mapTypes() {
    return
      array(
        'roadmap' => t('Roadmap'),
        'satellite' => t('Satellite'),
        'hybrid' => t('Hybrid'),
        'terrain' => t('Terrain'),
      );
  }
}
