<?php

/**
 * @file
 * Contains \Drupal\crosswords\EventSubscriber\CrosswordsContenttypeRedirect
 */

namespace Drupal\crosswords\EventSubscriber;

use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CrosswordsContenttypeRedirect implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // This announces which events you want to subscribe to.
    // We only need the request event for this example.  Pass
    // this an array of method names
    return([
      KernelEvents::REQUEST => [
          ['contentTypeRedirect'],
      ]
    ]);
  }

  /**
   * Redirect requests for crosswords node detail pages to crosswords/{nid}.
   *
   * @param GetResponseEvent $event
   * @return void
   */
  public function contentTypeRedirect(GetResponseEvent $event) {
    $request = $event->getRequest();
    // This is necessary because this also gets called on
    // node sub-tabs such as "edit", "revisions", etc.  This
    // prevents those pages from redirected.
    if ($request->attributes->get('_route') !== 'entity.node.canonical') {
      return;
    }

    // Only redirect a certain content type.
    if ($request->attributes->get('node')->getType() !== 'crosswords') {
      return;
    }
    // setting the destination.
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $redirectToUrl = $host . '/crosswords/' . $request->attributes->get('node')->id();
    $response = new RedirectResponse($redirectToUrl, 301);
    $response->send();
    return;
  }

}
