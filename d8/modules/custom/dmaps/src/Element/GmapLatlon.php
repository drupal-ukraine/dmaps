<?php
/**
 * @file
 * Contains \Drupal\dmaps\Element\GmapLatlon.
 */

namespace Drupal\dmaps\Element;

use Drupal\Core\Render\Element\FormElement;


/**
 * Provides gmap form element.
 *
 * @FormElement("gmap_latlon")
 */
class GmapLatlon extends FormElement {

  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#gmap_newtype' => 'textfield',
      '#process' => [
        [$class, 'processGmapControl'],
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

}
?>
