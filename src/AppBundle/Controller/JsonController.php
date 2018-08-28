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

class JsonController extends Controller
{
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
        	return new Response(htmlspecialchars(json_encode($all_news), ENT_HTML5));
        }
         /**
         *
         * @Route("/json_news_filter_header", name="json_news_filter_header")
         */

         public function json_news_filter_headerAction(Request $request,EntityManagerInterface $em)
         {
         	$count = $em->getRepository('AppBundle:News')->createQueryBuilder('u')
         	->orderBy("u.header",'ASC')
         	->getQuery();
         	$all_news = $count->getResult();
         	return new Response(htmlspecialchars(json_encode($all_news), ENT_HTML5));
         }
        /**
         *
         * @Route("/json_news_filter/{date_fr}/{date_t}", name="json_news_filter")
         */

        public function json_news_filterAction($date_fr, $date_t, Request $request,EntityManagerInterface $em)
        {
        	$date_from = new DateTime('2018-'.$date_fr.'-27');
        	$date_to = new DateTime('2018-'.$date_t.'-30');
        	$flag = new DateTime();
        	if($date_from>$date_to){
        		$flag = $date_from;
        		$date_from = $date_to;
        		$date_to=$flag;
        	}
        	$count = $em->getRepository('AppBundle:News')->createQueryBuilder('u')
        	->andWhere('u.createdAt > :date_start')
        	->andWhere('u.createdAt < :date_end')
        	->setParameter('date_start', $date_from->format('Y-m-d'))
        	->setParameter('date_end',   $date_to->format('Y-m-d'))
        	->getQuery();
        	$all_news = $count->getResult();
        	return new Response(htmlspecialchars(json_encode($all_news), ENT_HTML5));
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
