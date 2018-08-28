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

class AdminController extends Controller
{
	    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin", name="admin")
     */
      public function admin(EntityManagerInterface $em, Request $request)
      {
        $basePath = $this->getParameter('kernel.project_dir');
        $dir = $basePath . DIRECTORY_SEPARATOR . '/web';
        $news = new News();
        $form_news = $this->createForm(NewsType::class, $news);
        $form_news->handleRequest($request);
        if ($form_news->isSubmitted() && $form_news->isValid()) {
          $em = $this ->getDoctrine()->getManager();
          $news = $form_news->getData();
          $file = $form_news['Preview']->getData();
          $filename=rand(1, 99999).'.jpg';
          $file->move($dir, $filename);
          $news->setPreview($filename);
          $em->persist($news);
          $em->flush();
          return $this->redirectToRoute('admin');
        }
        $basePath = $this->getParameter('kernel.project_dir');
        $dir = $basePath . DIRECTORY_SEPARATOR . '/web';
        $count = $em->getRepository('AppBundle:Pictures')->createQueryBuilder('u')
        ->select('count(u.id)')
        ->getQuery()
        ->getSingleScalarResult();
        $count++;
        if(isset($_POST['qwe'])){
          $tags=new CrossTable();
          $tags->setNewsId($count++);
          $tags->setTegId($_POST['cur']);
          $em = $this ->getDoctrine()->getManager();
          $pictures= new Pictures();
          $file = $request->files;
          $filename =rand(1, 99999).'.jpg';;
          $uploadedFile = $file->get('tz');
          $file = $uploadedFile->move($dir, $filename);
          $pictures->setImg($filename);        
          $pictures->setDescription($_POST['Last_name']);
          $em->persist($pictures);
          $em->flush();
          $em->persist($tags);
          $em->flush();
          return $this->redirectToRoute('admin');
        }
        $all_news = $em->getRepository('AppBundle:News')->findAll();
        $all_pictures = $em->getRepository('AppBundle:Pictures')->findAll();
        $all_tags = $em->getRepository('AppBundle:Tag')->findAll();
        /*
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator_new  = $this->get('knp_paginator');
        $result = $paginator_new->paginate(
          $all_news, 
          $request->query->getInt('page',1),
          $request->query->getInt('limit',5)      
          );
        return $this->render('default/admin.html.twig', [
          'all_news' => $result,
          'all_pictures' => $all_pictures,
          'all_tags' => $all_tags,
          'form_news' => $form_news->createView(),
          ]);
      }
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin_update/{slug}", name="admin_update")
     */
    public function showAction($slug,Request $request,EntityManagerInterface $em)
    {
      $basePath = $this->getParameter('kernel.project_dir');
      $dir = $basePath . DIRECTORY_SEPARATOR . '/web';
      $news = $em->getRepository('AppBundle:News')->find($slug);
      $form = $this->createForm(NewsType::class, $news);
      $form->handleRequest($request);
      $name=$news->getPreview();
      if ($form->isSubmitted() && $form->isValid()) {
        $file = $form['Preview']->getData();
        if(file_exists($file)){
          $filename=rand(1, 99999).'.jpg';
          $file->move($dir, $filename);
          $news = $form->getData();
          $news->setPreview($filename);
        }
        else{
          $news = $form->getData();
        }
        $em->flush();
        return $this->redirectToRoute('admin');
      }
      return $this->render('default/edit.html.twig', [
       'form' => $form->createView(),
       'filname'=>$name
       ]);
    }
     /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin_update_pictures/{slug}", name="admin_update_pictures")
     */
     public function update_pictureAction($slug,Request $request,EntityManagerInterface $em)
     {
      $basePath = $this->getParameter('kernel.project_dir');
      $dir = $basePath . DIRECTORY_SEPARATOR . '/web';
      $pictures = $em->getRepository('AppBundle:Pictures')->find($slug);
      $tags = $em->getRepository('AppBundle:Tag')->findAll();
      $form = $this->createForm(PicturesType::class, $pictures);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $file = $form['Img']->getData();
        if(file_exists($file)){
          $filename=rand(1, 99999).'.jpg';
          $file->move($dir, $filename);
          $news = $form->getData();
          $news->setImg($filename);
        }
        else{
          $pictures = $form->getData();

        }
        $em->flush();
        return $this->redirectToRoute('admin');
      }
      
      return $this->render('default/edit_picture.html.twig', [
       'form' => $form->createView(),
       'all_tags' => $tags,
       'id_picture'=>$slug,
       ]);
    }
    /**
     * @Security("has_role('ROLE_ADMIN')")
     *@Route("/admin_tag_action/{slug}", name="admin_tag_action")
     */
    public function add_tag_Action($slug,Request $request,EntityManagerInterface $em)
    {
      if(isset($_POST['tag']) && isset($_POST['cur'])){
        $tag=new CrossTable();
        $tag->setNewsId($slug);
        $tag->setTegId($_POST['cur']);
        $em->persist($tag);
        $em->flush();
        return $this->redirectToRoute('admin');
      }
      return $this->redirectToRoute('admin');
    }
    /**
     * @Security("has_role('ROLE_ADMIN')")
     *@Route("/admin_remove/{slug}", name="admin_remove")
     */
    public function remove_new_Action($slug,Request $request,EntityManagerInterface $em)
    {
      $news = $em->getRepository('AppBundle:News')->findOneById($slug);
      $em->remove($news);
      $em->flush();
      return $this->redirectToRoute('admin');
    }
   /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin_clear_teg/{slug}", name="admin_clear_teg")
     */
   public function clear_new_Action($slug,Request $request,EntityManagerInterface $em)
   {
     $query = $em->createQuery(
      'UPDATE AppBundle:CrossTable p
      SET p.tegId=null
      WHERE p.newsId = :price'
      )->setParameter('price', $slug);
     $query->getResult();
     return $this->redirectToRoute('admin');
   }

 }

