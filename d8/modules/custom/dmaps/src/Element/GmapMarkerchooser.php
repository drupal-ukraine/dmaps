<?php
/**
 * @file
 * Contains \Drupal\dmaps\Element\GmapMarkerchooser.
 */

namespace Drupal\dmaps\Element;

use Drupal\Core\Render\Element\FormElement;


/**
 * Provides gmap_markerchooser form element.
 *
 * @FormElement("gmap_markerchooser")
 */
class GmapMarkerchooser extends FormElement {

  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'process_gmap_markerchooser'],
      ],
    ];
  }

  /**
   * Marker chooser #process function.
   */
  function processGmapMarkerchooser($element) {
    // @todo Converted process_gmap_markerchooser function should be here.
    return $element;
  }

}
?>
