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


     return $this->render('default/index.html.twig');
 }
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin", name="admin")
     */
    public function admin(EntityManagerInterface $em, Request $request)
    {

        $news = new News();
        $form_news = $this->createForm(NewsType::class, $news);
        $form_news->handleRequest($request);
        if ($form_news->isSubmitted() && $form_news->isValid()) {
            $em = $this ->getDoctrine()->getManager();
            $news = $form_news->getData();
            $em->persist($news);
            $em->flush();
            return $this->redirectToRoute('admin');
        }
        $basePath = $this->getParameter('kernel.project_dir');
        $dir = $basePath . DIRECTORY_SEPARATOR . '/web';
        if(isset($_POST['qwe'])){
            $tags=new CrossTable();
            $count = $em->getRepository('AppBundle:Pictures')->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
            var_dump($count);
            $count=$count+1;
            $tags->setNewsId($count);
            move_uploaded_file($_FILES['tz']['name'],$dir);
            $em = $this ->getDoctrine()->getManager();
            $pictures= new Pictures();
            $pictures->setImg($_FILES['tz']['name']);
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
        $news = $em->getRepository('AppBundle:News')->find($slug);
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $news = $form->getData();
          $em->flush();
          return $this->redirectToRoute('admin');
      }
      return $this->render('default/edit.html.twig', [
       'form' => $form->createView()
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
        if(isset($_POST['tag'])){
            $tag=new CrossTable();
            $tag->setNewsId($slug);
            $tag->setTegId($_POST['cur']);
            $em->persist($tag);
            $em->flush();
        }
        if ($form->isSubmitted() && $form->isValid()) {
          $pictures = $form->getData();
          $file = $form['Img']->getData();
          $filename=rand(1, 99999).'.jpg';
          $file->move($dir, $filename);
          $pictures->setImg($filename);
          $em->flush();
          return $this->redirectToRoute('admin');
      }
      return $this->render('default/edit_picture.html.twig', [
       'form' => $form->createView(),
       'all_tags' => $tags,
       'id_picture'=>$slug
       ]);
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
  /**
     * @Route("/json_pictures", name="json_pictures")
     */
  public function json_picture_Action(Request $request,EntityManagerInterface $em)
  {
      $connection = $em->getConnection();
      $count = $em->getRepository('AppBundle:Pictures')->createQueryBuilder('u')
      ->select('count(u.id)')
      ->getQuery()
      ->getSingleScalarResult();
      for($i=1; $i<=$count; $i++)
      {
          $statement = $connection->prepare("select tag.id, tag.name from tag inner join cross_table on cross_table.teg_id=tag.id where cross_table.picture_id=:p_id");
          $statement->bindValue('p_id', $i);
          $statement->execute();
          $results = $statement->fetchAll();
          $all_pic = $em->getRepository('AppBundle:Pictures')->findOneById($i);
          $foo = array(
              'id'=>$all_pic->getId(),
              'tags' => $results, 
              'img' => $all_pic->getImg(),
              'description' => $all_pic->getDescription()
              );
          $arr[]=$foo;
      }
      return new Response(json_encode($arr));
  }
  
        /**
         *
         * @Route("/json_news", name="json_news")
         */

        public function json_news_Action(Request $request,EntityManagerInterface $em)
        {

           $all_news = $em->getRepository('AppBundle:News')->findAll();
           return new Response(json_encode($all_news));
       }

                   /**
                     *
                     * @Route("/search_with_tags/{slug}", name="search_with_tags")
                     */

                   public function search_with_tagsAction(Request $request,EntityManagerInterface $em, $slug)
                   {
                    $connection = $em->getConnection();
                    $statement = $connection->prepare("select picture_id from (select * from tag inner join cross_table on cross_table.teg_id=tag.id where tag.name=:tag_name) as t1 inner join (select id, img, description FROM pictures order by id
                        ) as t2 
                        ON t1.picture_id = t2.id");
                    $statement->bindValue('tag_name', $slug);
                    $statement->execute();
                    $results = $statement->fetchAll();
                    foreach ($results as $key => $value) {
                        $tags = $connection->prepare("select tag.id, tag.name from tag inner join cross_table on cross_table.teg_id=tag.id where cross_table.picture_id=:p_id");
                        $tags->bindValue('p_id', $value['picture_id']);
                        $tags->execute();
                        $res = $tags->fetchAll();
                        $all_pic = $em->getRepository('AppBundle:Pictures')->findOneById($value['picture_id']);
                        $foo = array(
                            'id'=>$all_pic->getId(),
                            'tags' => $res, 
                            'img' => $all_pic->getImg(),
                            'description' => $all_pic->getDescription()
                            );
                        $arr[]=$foo;
                    }
                    return new Response(json_encode($arr));
                }
            }
