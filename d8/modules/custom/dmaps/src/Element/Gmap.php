<?php
/**
 * @file
 * Contains \Drupal\dmaps\Element\Gmap.
 */

namespace Drupal\dmaps\Element;

use Drupal\Core\Render\Element\FormElement;


/**
 * Provides gmap form element.
 *
 * @FormElement("gmap")
 */
class Gmap extends FormElement {

  public function getInfo() {
    $class = get_class($this);
    return [
      // This isn't a *form* input.
      '#input' => FALSE,
      // @todo: Need include default settings.
      //'#gmap_settings' => array_merge(gmap_defaults(), [
      '#gmap_settings' => [
        'points' => [],
        'pointsOverlays' => [],
        'lines' => [],
      ],
      '#attached' => [
        'library' => ['dmap/gmap'],
      ],
      '#pre_render' => [
        [$class, 'preRenderMap'],
      ],
      '#theme' => 'gmap',
    ];
  }

  /**
   * Pre render function to make sure all required JS is available.
   *
   * Depending on the display's behavior.
   */
  function preRenderMap($element) {
    // @todo Converted _gmap_pre_render_map function should be here.
    return $element;
  }

}
?>
