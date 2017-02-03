<?php

namespace Drupal\crosswords\EventSubscriber;

/**
 * @file
 * Contains \Drupal\crosswords\EventSubscriber\CrosswordsContenttypeRedirect.
 */

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirect Crosswords records to a predefined path.
 */
class CrosswordsContenttypeRedirect implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // This announces which events you want to subscribe to.
    return([
      KernelEvents::REQUEST => [
          ['contentTypeRedirect'],
      ],
    ]);
  }

  /**
   * Redirect requests for crosswords node detail pages to crosswords/{nid}.
   *
   * @param GetResponseEvent $event Response event.
   *
   * @return NULL Nothing to return.
   */
  public function contentTypeRedirect(GetResponseEvent $event) {
    $request = $event->getRequest();
    // This prevents those pages from redirected.
    if ($request->attributes->get('_route') !== 'entity.node.canonical') {
      return NULL;
    }
    // Only redirect a certain content type.
    if ($request->attributes->get('node')->getType() !== 'crosswords') {
      return NULL;
    }
    // Setting the destination.
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $redirectToUrl = $host . '/crosswords/' . $request->attributes->get('node')->id();
    $response = new RedirectResponse($redirectToUrl, 301);
    $response->send();
    return NULL;
  }

}
