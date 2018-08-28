<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\News;
use AppBundle\Entity\Tag;
use AppBundle\Entity\CrossTable;
use AppBundle\Entity\Pictures;
use AppBundle\Form\Type\NewsType;
use AppBundle\Form\Type\TagType;
use AppBundle\Form\Type\PicturesType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \DateTime;

class DefaultController extends Controller
{
	/**
     * @Route("/", name="homepage")
     */
  public function indexAction()
  {

   return $this->render('default/index.html.twig', array(
    'months' => array ('Январь' => 1,
      'Февраль' => 2,
      'Март' => 3,
      'Апрель' => 4,
      'Март' => 5,
      'Июнь' => 6,
      'Июль' => 7,
      'Август' => 8,
      'Сентябрь' => 9,
      'Октябрь' => 10,
      'Ноябрь' => 11,
      'Декабрь' => 12)));
 }
 
}
