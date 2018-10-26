<?php

namespace CiviExtManagerBundle;

use CiviExtManagerBundle\Event\FindExtensionsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExtUrlSource
 * @package CiviExtManagerBundle
 *
 * The mock source handles requests like `/extdir/ver=1.2.3/`.
 * It simply loads the extension list from the `mock-feed.json` file.
 *
 * NOTE: This filter does *not* interpret
 */
class ExtUrlSource implements EventSubscriberInterface {

  protected $url;

  /**
   * ExtUrlSource constructor.
   * @param $url
   */
  public function __construct($url) {
    $this->url = $url;
  }

  public function onFindExtensions(FindExtensionsEvent $e) {
    if (!empty($e->extensions)) {
      return;
    }

    $url = strtr($this->url, [
      '{filterExpr}' => $e->getFilterExpr(),
    ]);

    $e->extensions = json_decode(file_get_contents($url), 1);
  }

  public static function getSubscribedEvents() {
    return [
      FindExtensionsEvent::EVENT_NAME => [
        ['onFindExtensions', FindExtensionsEvent::PRIORITY_LOAD]
      ],
    ];
  }

}
