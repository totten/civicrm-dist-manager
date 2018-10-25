<?php

namespace CiviExtManagerBundle;

use CiviExtManagerBundle\Event\FindExtensionsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExtMockSource
 * @package CiviExtManagerBundle
 *
 * The mock source handles requests like `/extdir/ver=1.2.3|mock=1/`.
 * It simply loads the extension list from the `mock-feed.json` file.
 *
 * NOTE: This filter does *not* interpret
 */
class ExtMockSource implements EventSubscriberInterface {

  public function onFindExtensions(FindExtensionsEvent $e) {
    $filters = FilterCodex::decode($e->getFilterExpr());
    if (isset($filters['mock']) && $filters['mock'] == '1') {
      $mocks = json_decode(file_get_contents(__DIR__ . '/Resources/mock-feed.json'), 1);
      $e->extensions = array_merge($e->extensions, $mocks);
    }
  }

  public static function getSubscribedEvents() {
    return [
      FindExtensionsEvent::EVENT_NAME => [
        [
          'onFindExtensions',
          FindExtensionsEvent::PRIORITY_LOAD
        ]
      ],
    ];
  }

}
