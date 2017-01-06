<?php

namespace Drupal\geoip_content\Controller;

/**
  @file
  Contains \Drupal\geoip_content\Controller\GeoipContentController.
 */
use Drupal\Core\Controller\ControllerBase;

class GeoipContentController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function showData() {
    $visitorIp = \Drupal::request()->getClientIp();
    if ($visitorIp === '127.0.0.1') {
      $visitorIp = '14.140.162.242';
    }
    $data = \Drupal::service('geoip.geolocation')->geolocate($visitorIp)->getCountryCode();
    echo 'Hello';
    die();
  }

}
