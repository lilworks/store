<?php

namespace AppBundle\EventListener;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface {

    private $container;
    private $em;

    public function __construct($container,\Doctrine\ORM\EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function logout(Request $Request, Response $Response, TokenInterface $Token) {

        $session_id =  $this->container->get('session')->getId() ;
        $session = $this->em->getRepository('AppBundle:Session')->find($session_id);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if( $user && $session ){
            $user = $this->em->getRepository('AppBundle:User')->find($user->getId());
            if(!$session->getUser()){
                $session->setUser(null);
                $user->removeSession($session);
                $this->em->persist($session);
                $this->em->flush();
            }

        }

    }

}