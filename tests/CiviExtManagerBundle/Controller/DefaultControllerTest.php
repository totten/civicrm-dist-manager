<?php

namespace CiviExtManagerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

  public function testIndex() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/');
    $this->assertContains('org.civicoop.smsapi', $client->getResponse()->getContent());
    $this->assertContains('org.wikimedia.unsubscribeemail', $client->getResponse()->getContent());
    // FIXME: check exact HTML format of current responses
  }

  public function testSingleJson() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/single');
    $data = json_decode($client->getResponse()->getContent(), TRUE);
    $this->assertTrue(!empty($data['org.civicoop.smsapi']));
    $this->assertTrue(!empty($data['org.wikimedia.unsubscribeemail']));
    $this->assertTrue(!isset($data['mock.doesnotexist']));
  }

  public function testExtInfoXml() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/org.civicoop.smsapi.xml');
    $xml = simplexml_load_string($client->getResponse()->getContent());
    $this->assertEquals('org.civicoop.smsapi', $xml['key']);
  }

  public function testExtInfoXml_missingXmlExtension() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/org.civicoop.smsapi');
    $this->assertEquals(404, $client->getResponse()->getStatusCode());
    // FIXME: check exact XML/HTML format of current responses
  }

  public function testExtInfoXml_unknownExtension() {
    $client = static::createClient();
    $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/mock.doesnotexist.xml');
    $this->assertEquals(404, $client->getResponse()->getStatusCode());
    // FIXME: check exact XML/HTML format of current responses
  }

}
