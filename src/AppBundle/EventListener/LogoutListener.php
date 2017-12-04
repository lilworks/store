<?php

namespace AppBundle\EventListener;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener  implements LogoutHandlerInterface
{

    private $container;
    private $em;

    public function __construct($container,\Doctrine\ORM\EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }


    public function logout(Request $Request, Response $Response, TokenInterface $Token) {

        $session_id =  $this->container->get('session')->getId() ;
        $session = $this->em->getRepository('AppBundle:Session')->findOneById($session_id);
        $basket  = $session->getBasket();
        if(!is_null($basket)){
            $basket->setUser(null);
            $basket->setShippingAddress(null);
            $basket->setBillingAddress(null);
            $this->em->persist($basket);
            #$user->removeBasket($basket);
            $this->em->flush();
        }

    }

}