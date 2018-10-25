<?php

namespace CiviExtManagerBundle\Controller;

use CiviExtManagerBundle\ExtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {
  /**
   * @Route("/{filters}")
   * @Route("/{filters}/")
   */
  public function indexAction($filters) {
    return $this->render('CiviExtManagerBundle:Default:index.html.twig', [
      'exts' => array_keys($this->getExtRepo()->get($filters)),
    ]);
  }

  /**
   * @Route("/{filters}/single")
   */
  public function getSingleJsonAction($filters) {
    return $this->createJson($this->getExtRepo()->get($filters));
  }

  /**
   * @Route("/{filters}/{file}")
   */
  public function getFileAction($filters, $file) {
    if (empty($file)) {
      return $this->indexAction();
    }

    if (!preg_match(';^(.*)\.xml$;', $file, $matches)) {
      return $this->createXml('<error>Not found. Malformed extension key.</error>', 404); // FIXME: check old response format
    }

    $item = $this->getExtRepo()->getExtension($filters, $matches[1]);
    if ($item === NULL) {
      return $this->createXml('<error>Not found. Unknown extension key.</error>', 404); // FIXME: check old response format
    }

    return $this->createXml($item, 200);
  }

  /**
   * @return ExtRepository
   */
  protected function getExtRepo() {
    return $this->container->get('civi_ext_manager.repo');
  }

  /**
   * @param mixed $data
   * @param int $status
   * @return \Symfony\Component\HttpFoundation\Response
   */
  protected function createJson($data, $status = 200) {
    return new Response(json_encode($data), $status, array(
      'Content-type' => 'application/json',
    ));
  }

  /**
   * @param string $xmlString
   * @param int $status
   * @return \Symfony\Component\HttpFoundation\Response
   */
  protected function createXml($xmlString = NULL, $status = 200) {
    return new Response($xmlString, $status, array(
      'Content-type' => 'text/xml',
    ));
  }

}
