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
   * @var int
   *   Number of seconds to retain a cached record.
   */
  protected $ttl;

  /**
   * RevDocRepository constructor.
   * @param \Doctrine\Common\Cache\Cache $cache
   */
  public function __construct(EventDispatcherInterface $dispatcher, \Doctrine\Common\Cache\Cache $cache, $ttl) {
    $this->dispatcher = $dispatcher;
    $this->cache = $cache;
    $this->ttl = $ttl;
  }

  /**
   * @param string $filters
   * @return array
   *   Array(string $extKey => string $xml).
   */
  public function get($filters) {
    $cacheKey = 'ext_' . $filters;
    $data = $this->cache->fetch($cacheKey);
    if (FALSE === $data) {
      $event = new FindExtensionsEvent($filters);
      $this->dispatcher->dispatch(FindExtensionsEvent::EVENT_NAME, $event);
      $data = $event->extensions;
      $this->cache->save($cacheKey, $data, $this->ttl);
    }
    return $data;
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
