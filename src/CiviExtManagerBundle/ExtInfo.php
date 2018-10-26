<?php

namespace CiviExtManagerBundle;
use CiviExtManagerBundle\Exception\XmlParseException;

/**
 * Class ExtInfo
 * @package CiviExtManagerBundle
 */
class ExtInfo {

  /**
   * Convert the info.xml string to an object.
   *
   * @param $string
   *
   * @return \SimpleXMLElement
   * @throws XmlParseException
   */
  public static function decode($string) {
    $oldUseInternalErrors = libxml_use_internal_errors();
    libxml_use_internal_errors(TRUE);

    $xml = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
    if ($xml === FALSE) {
      throw new XmlParseException(self::formatErrors(libxml_get_errors()));
    }

    libxml_use_internal_errors($oldUseInternalErrors);

    return $xml;
  }

  /**
   * Convert the info.xml object to a string.
   *
   * @param \SimpleXMLElement $xml
   * @return string|FALSE
   */
  public static function encode($xml) {
    return $xml->asXML();
  }

  /**
   * @param $errors
   *
   * @return string
   */
  protected static function formatErrors($errors) {
    $messages = array();

    foreach ($errors as $error) {
      if ($error->level != LIBXML_ERR_ERROR && $error->level != LIBXML_ERR_FATAL) {
        continue;
      }

      $parts = array();
      if ($error->file) {
        $parts[] = "File=$error->file";
      }
      $parts[] = "Line=$error->line";
      $parts[] = "Column=$error->column";
      $parts[] = "Code=$error->code";

      $messages[] = implode(" ", $parts) . ": " . trim($error->message);
    }

    return implode("\n", $messages);
  }

}
