<?php

namespace CiviExtManagerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

  public function testIndex() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/');
    $this->assertContains('mock.ext1', $client->getResponse()->getContent());
    // FIXME: check exact HTML format of current responses
  }

  public function testSingleJson() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/single');
    $data = json_decode($client->getResponse()->getContent(), TRUE);
    $this->assertTrue(!empty($data['mock.ext1']));
    $this->assertTrue(!empty($data['mock.ext2']));
    $this->assertTrue(!isset($data['mock.doesnotexist']));
  }

  public function testExtInfoXml() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/mock.ext2.xml');
    $this->assertRegExp(';^<extension key=\"mock.ext2;',
      $client->getResponse()->getContent());
  }

  public function testExtInfoXml_missingXmlExtension() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/mock.ext2');
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
