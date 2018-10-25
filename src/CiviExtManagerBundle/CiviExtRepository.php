<?php
namespace CiviExtManagerBundle;

class CiviExtRepository {

  /**
   * @var \Doctrine\Common\Cache\Cache
   */
  protected $cache;

  /**
   * RevDocRepository constructor.
   * @param \Doctrine\Common\Cache\Cache $cache
   */
  public function __construct(\Doctrine\Common\Cache\Cache $cache) {
    $this->cache = $cache;
  }

  /**
   * @param string $filters
   * @return array
   *   Array(string $extKey => string $xml).
   */
  public function get($filters) {
    return [
      'mock.ext1' => '<extension key="mock.ext1"></extension>',
      'mock.ext2' => '<extension key="mock.ext2"></extension>'
    ];
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