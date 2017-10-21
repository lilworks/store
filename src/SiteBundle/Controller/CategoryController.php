<?php
namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{

    public function allAction()
    {

            $categories = $this->getDoctrine()->getRepository('LilWorksStoreBundle:Category')->findBy(
                array(
                    'isPublished'=>1
                )
            );

        return $this->render('SiteBundle:Category:all.html.twig',array(
            'categories'=>$categories
        ));
    }
}