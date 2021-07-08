<?php
/**
 * @file
 * Contains \Drupal\dmaps\Element\GmapDimension.
 */

namespace Drupal\dmaps\Element;

use Drupal\Core\Render\Element\FormElement;


/**
 * Provides gmap_dimension form element.
 *
 * @FormElement("gmap_dimension")
 */
class GmapDimension extends FormElement {

  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#gmap_newtype' => 'textfield',
      '#process' => [
        [$class, 'processGmapControl'],
      ],
      '#element_validate' => [
        [$class, 'dimensionValidate'],
      ],
    ];
  }

  /**
   * Generic gmap control #process function.
   */
  function processGmapControl($element, &$form_state, $complete_form) {
    // @todo Converted process_gmap_control function should be here.
    return $element;
  }

  /**
   * Ensure a textfield is a valid css dimension string.
   */
  function dimensionValidate(&$elem, &$form_state) {
    // @todo Converted gmap_dimension_validate function should be here.
  }

}
?>
