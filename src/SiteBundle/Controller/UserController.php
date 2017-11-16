<?php
namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{


    public function SmallAction()
    {
        $user = $this->getUser();

        return $this->render('SiteBundle:User:small.html.twig',array(
            'user'=>$user
        ));
    }
}