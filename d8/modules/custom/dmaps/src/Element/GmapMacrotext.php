<?php
/**
 * @file
 * Contains \Drupal\dmaps\Element\GmapMacrotext.
 */

namespace Drupal\dmaps\Element;

use Drupal\Core\Render\Element\FormElement;


/**
 * Provides gmap_macrotext form element.
 *
 * @FormElement("gmap_macrotext")
 */
class GmapMacrotext extends FormElement {

  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#gmap_newtype' => 'textarea',
      '#process' => [
        [$class, 'processGmapControl'],
      ],
      '#attached' => [
        'library' => ['dmap/gmap_macrotext'],
      ],
      '#theme' => 'textarea',
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
