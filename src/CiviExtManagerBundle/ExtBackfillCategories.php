<?php

namespace CiviExtManagerBundle;

use CiviExtManagerBundle\Event\FindExtensionsEvent;
use CiviExtManagerBundle\Exception\XmlParseException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExtBackfillCategories
 * @package CiviExtManagerBundle
 *
 * This listener is intended to backfill the category list -- e.g. if an
 * extension's `info.xml` does not have a `<categories>`, then we load
 * the category info from `app/config/ext-categories.json`.
 */
class ExtBackfillCategories implements EventSubscriberInterface {

  protected $categoriesFile;

  /**
   * @var LoggerInterface
   */
  protected $log;

  /**
   * ExtBackfillCategories constructor.
   * @param $categoriesFile
   */
  public function __construct($categoriesFile, $log) {
    $this->categoriesFile = $categoriesFile;
    $this->log = $log;
  }

  public function onFindExtensions(FindExtensionsEvent $e) {
    $categories = json_decode(file_get_contents($this->categoriesFile), 1);

    // Items which are both (a) in the directory and (b) have backfilled categories.
    $extKeys = array_intersect(
      array_keys($e->extensions),
      array_keys($categories)
    );

    foreach ($extKeys as $extKey) {
      try {
        $info = ExtInfo::decode($e->extensions[$extKey]);
      }
      catch (XmlParseException $e) {
        $this->log->warning("Failed to parse info.xml for \"{key}\"", [
          'key' => $extKey,
        ]);
        continue;
      }

      foreach ($info->category as $catXml) {
        continue 2; // info.xml category takes precedence.
      }

      // FIXME: What is the schema for putting in the category?
      $info->addChild('category', $categories[$extKey]);

      $e->extensions[$extKey] = ExtInfo::encode($info);
    }
  }

  public static function getSubscribedEvents() {
    return [
      FindExtensionsEvent::EVENT_NAME => [
        ['onFindExtensions', FindExtensionsEvent::PRIORITY_FILTER]
      ],
    ];
  }

}
