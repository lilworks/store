<?php
namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{

    public function allAction()
    {


        $categories =  $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('LilWorksStoreBundle:Category','c')
            ->join('c.products','p')
            ->where('c.isPublished = 1')
            ->having('COUNT(p) > 0')
            ->groupBy('c.id ')
            ->getQuery()
            ->getResult()
            ;

        return $this->render('SiteBundle:Category:all.html.twig',array(
            'categories'=>$categories
        ));
    }
}