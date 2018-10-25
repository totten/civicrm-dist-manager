<?php

namespace CiviExtManagerBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class FindExtensionsEvent
 * @package CiviExtManagerBundle\Event
 *
 * Listeners should add matching extensions and/or filter the matching
 * extensions.
 */
class FindExtensionsEvent extends Event {

  const PRIORITY_LOAD = 100;
  const PRIORITY_FILTER = 200;
  const EVENT_NAME = 'civi.extmgr.find_extensions';

  /**
   * @var array
   *   Array(string $key => string $xml).
   */
  public $extensions;

  /**
   * @var string
   *   Ex:
   */
  protected $filterExpr;

  /**
   * FindExtensionsEvent constructor.
   *
   * @param string $filterExpr
   *   Ex: 'ver=1.2.3|cms=FooBar'.
   */
  public function __construct($filterExpr) {
    $this->filterExpr = $filterExpr;
    $this->extensions = [];
  }

  /**
   * @return string
   */
  public function getFilterExpr() {
    return $this->filterExpr;
  }

}
