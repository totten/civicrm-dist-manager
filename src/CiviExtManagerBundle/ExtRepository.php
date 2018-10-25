<?php
namespace CiviExtManagerBundle;

use CiviExtManagerBundle\Event\FindExtensionsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExtRepository {

  /**
   * @var \Doctrine\Common\Cache\Cache
   */
  protected $cache;

  protected $dispatcher;

  /**
   * RevDocRepository constructor.
   * @param \Doctrine\Common\Cache\Cache $cache
   */
  public function __construct(\Doctrine\Common\Cache\Cache $cache, EventDispatcherInterface $dispatcher) {
    $this->cache = $cache;
    $this->dispatcher = $dispatcher;
  }

  /**
   * @param string $filters
   * @return array
   *   Array(string $extKey => string $xml).
   */
  public function get($filters) {
    $event = new FindExtensionsEvent($filters);
    $this->dispatcher->dispatch(FindExtensionsEvent::EVENT_NAME, $event);
    return $event->extensions;
  }

  /**
   * @param array $filters
   * @param string $key
   *   Ex: 'org.civicrm.flexmailer'.
   * @return string|NULL
   *   XML content, or NULL if not available
   */
  public function getExtension($filters, $key) {
    $all = $this->get($filters);
    return isset($all[$key]) ? $all[$key] : NULL;
  }

}
