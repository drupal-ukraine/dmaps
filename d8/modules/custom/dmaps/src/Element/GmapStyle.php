<?php
/**
 * @file
 * Contains \Drupal\dmaps\Element\GmapStyle.
 */

namespace Drupal\dmaps\Element;

use Drupal\Core\Render\Element\FormElement;


/**
 * Provides gmap_style form element.
 *
 * @FormElement("gmap_style")
 */
class GmapStyle extends FormElement {

  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#tree' => TRUE,
      '#gmap_style_type' => 'poly',
      '#process' => [
        [$class, 'processStyle'],
      ],
    ];
  }

  /**
   * Style fieldset #process function.
   */
  function processStyle($element) {
    // @todo Converted process_gmap_style function should be here.
    return $element;
  }

}
?>
