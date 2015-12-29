<?php

/**
 * @file
 * Base map provider.
 */

/**
 * Class MapProviderBase
 */
class MapProviderBase {
  /**
   * Build parameters.
   *
   * @param array $items
   *   Items array.
   *
   * @param array $settings
   *   Settings array.
   */
  public function buildParams(&$items, &$settings) {

  }

  public $apiUri = '';

  /**
   * Altering preset form.
   *
   * @param array $form
   *   Drupal form array.
   *
   * @param array $form_state
   *   Drupal form state array.
   *
   * @param array $preset_data
   *   Data array for preset.
   */
  public function presetFormAlter(&$form, &$form_state, $preset_data) {

  }

  /**
   * Summary settins markup.
   *
   * @param string $field
   *   Fiels machine name.
   *
   * @param array $instance
   *   Field instance.
   * @param string $view_mode
   *   View mode for a field.
   *
   * @param array $preset_data
   *   Data array for preset.
   */
  public function fieldFormatterSettingsSummary($field, $instance, $view_mode, $preset_data) {

  }
}
