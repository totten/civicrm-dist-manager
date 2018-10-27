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
    $this->assertTrue(!empty($data['mock.ext1']));
    $this->assertTrue(!isset($data['mock.doesnotexist']));
  }

  public function testSingleJson_backfillCategory() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/single');
    $data = json_decode($client->getResponse()->getContent(), TRUE);

    // For mock.ext1, the data from JSON is canonical. (It's the only data.)
    $this->assertTrue(!empty($data['mock.ext1']));
    $this->assertTrue(FALSE !== strpos($data['mock.ext1'], '<category>Category via JSON (1)</category>'), 'The <category> should be back-filled from JSON.');

    // For mock.ext2, the data from XML is canonical.
    $this->assertTrue(!empty($data['mock.ext2']));
    $this->assertTrue(FALSE !== strpos($data['mock.ext2'], '<category>Category via XML (2)</category>'), 'The <category> should come from XML.');
    $this->assertTrue(FALSE === strpos($data['mock.ext2'], '<category>Category via JSON (2)</category>'), 'The <category> should come from XML.');
  }

  public function testExtInfoXml() {
    $client = static::createClient();
    $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/org.civicoop.smsapi.xml');
    $xml = simplexml_load_string($client->getResponse()->getContent());
    $this->assertEquals('org.civicoop.smsapi', $xml['key']);
    $expectedXml = "<?xml version=\"1.0\"?>\n<extension key=\"org.civicoop.smsapi\" type=\"module\">\n  <file>smsapi</file>\n  <name>SMS API</name>\n  <description>API to send an sms to a contact</description>\n  <license>AGPL-3.0</license>\n  <maintainer>\n    <author>Jaap Jansma - CiviCooP</author>\n    <email>helpdesk@civicoop.org</email>\n  </maintainer>\n  <urls>\n    <url desc=\"Main Extension Page\">https://guthub.com/CiviCooP/org.civicoop.smsapi</url>\n    <url desc=\"documentation\">https://guthub.com/CiviCooP/org.civicoop.smsapi/README.md</url>\n    <url desc=\"Support\">http://civicoop.org</url>\n    <url desc=\"Licensing\">http://www.gnu.org/licenses/agpl-3.0.html</url>\n  </urls>\n  <releaseDate>2018-06-13</releaseDate>\n  <version>1.5</version>\n  <develStage>stable</develStage>\n  <compatibility>\n    <ver>4.3</ver>\n    <ver>4.4</ver>\n    <ver>4.5</ver>\n    <ver>4.6</ver>\n    <ver>4.7</ver>\n    <ver>5.0</ver>\n    <ver>5.1</ver>\n  </compatibility>\n  <comments>\n    Original developed by http://civicoop.org. Contributed by http://www.ixiam.com\n  </comments>\n  <civix>\n    <namespace>CRM/Smsapi</namespace>\n  </civix>\n<downloadUrl>https://github.com/CiviCooP/org.civicoop.smsapi/archive/v1.5.zip</downloadUrl></extension>\n";
    $this->assertEquals($expectedXml, $client->getResponse()->getContent());
  }

  public function testExtInfoXml_missingXmlExtension() {
    $client = static::createClient();
    $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/org.civicoop.smsapi');
    $this->assertEquals(404, $client->getResponse()->getStatusCode());
    // NOTE: Old behavior was return a long HTML document. Not likely that anyone was parsing it.
    $this->assertEquals('<error>Not found. Malformed extension key.</error>', $client->getResponse()->getContent());
  }

  public function testExtInfoXml_unknownExtension() {
    $client = static::createClient();
    $client->request('GET', '/extdir/ver=4.7.99|cms=Drupal|mock=1/mock.doesnotexist.xml');
    $this->assertEquals(404, $client->getResponse()->getStatusCode());
    // NOTE: Old behavior was return a long HTML document. Not likely that anyone was parsing it.
    $this->assertEquals('<error>Not found. Unknown extension key.</error>', $client->getResponse()->getContent());
  }

}
