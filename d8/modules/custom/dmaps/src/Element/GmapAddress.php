<?php
/**
 * @file
 * Contains \Drupal\dmaps\Element\GmapAddress.
 */

namespace Drupal\dmaps\Element;

use Drupal\Core\Render\Element\FormElement;


/**
 * Provides gmap_address form element.
 *
 * @FormElement("gmap_address")
 */
class GmapAddress extends FormElement {

  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#pre_render' => [
        [$class, 'processAddress'],
      ],
      '#attached' => [
        'library' => ['dmaps/gmap_address'],
      ],
      '#autocomplete_path' => '',
      '#theme' => 'textfield',
    ];
  }

  /**
   * Style fieldset #process function.
   */
  function processAddress($element) {
    // @todo Converted process_gmap_address function should be here.
    return $element;
  }

}
?>
