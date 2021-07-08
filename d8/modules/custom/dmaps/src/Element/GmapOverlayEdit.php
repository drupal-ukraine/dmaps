<?php
/**
 * @file
 * Contains \Drupal\dmaps\Element\GmapOverlayEdit.
 */

namespace Drupal\dmaps\Element;

use Drupal\Core\Render\Element\FormElement;


/**
 * Provides gmap_overlay_edit form element.
 *
 * @FormElement("gmap_overlay_edit")
 */
class GmapOverlayEdit extends FormElement {

  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'processGmapOverlayEdit'],
      ],
      '#attached' => [
        'library' => ['dmaps/gmap_overlay_edit'],
      ],
    ];
  }

  /**
   * Generic gmap_overlay_edit #process function.
   */
  function processGmapOverlayEdit($element, &$form_state, $complete_form) {
    // @todo Converted process_gmap_overlay_edit function should be here.
    return $element;
  }

}
?>
