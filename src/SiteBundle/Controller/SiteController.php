<?php
namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{


    public function userBasketSmallAction()
    {
        $basket = $this->get('site.basket')->getBasket();
        $user = $this->getUser();


        return $this->render('SiteBundle:Site:userbasketsmall.html.twig',array(
            'user'=>$user,
            'basket'=>$basket
        ));
    }
}