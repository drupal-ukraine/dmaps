<?php

/**
 * @file
 * Contains Drupal\dmaps\Breadcrumb\AdminPagesBreadcrumbBuilder.
 */

namespace Drupal\dmaps\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Link;

/**
 * Defines a custom breadcrumbs manager service.
 */
class AdminPagesGeocoderBreadcrumbBuilder implements BreadcrumbBuilderInterface {
  use StringTranslationTrait;
  use LinkGeneratorTrait;

  /**
   * @inheritdoc
   */
  public function applies(RouteMatchInterface $route_match) {
    if ($route_match->getRouteObject()) {
      return $route_match->getRouteName() == 'dmaps.locations.geocoder_options';
    }
    return FALSE;
  }

  /**
   * @inheritdoc
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $geocoder = $route_match->getParameter('service');
    $current_route = $route_match->getRouteName();

    $links = [
      Link::createFromRoute($this->t('Home'), '<front>'),
      Link::createFromRoute($this->t('Administration'), 'system.admin'),
      Link::createFromRoute($this->t('Configuration'), 'system.admin_config'),
      Link::createFromRoute($this->t('Dmaps'), 'dmaps.settings'),
      Link::createFromRoute($this->t('Geocoding'), 'dmaps.locations.geocoding_options'),
      Link::createFromRoute($this->t('Geocoding %service', ['%service' => $geocoder]), $current_route, [
        'iso' => $route_match->getParameter('iso'),
        'service' => $geocoder,
      ]),
    ];
    $breadcrumb->setLinks($links);
    return $breadcrumb;
  }
}
