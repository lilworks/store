<?php
namespace AppBundle\HttpFoundation\Session\Storage\Handler;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\Security\Core\User\UserInterface;

class UserPdoSessionHandler extends PdoSessionHandler
{

    protected  $em;



    public function __construct( $pdo, array $dbOptions = array(), $context,\Doctrine\ORM\EntityManager $em)
    {

        $this->em = $em;
        $this->context = $context;



        parent::__construct($pdo, $dbOptions);

    }

    /**
     * {@inheritDoc}
     */
/*
    public function gc($max)
    {
        var_dump("GC");
        $session = $this->em->getRepository('AppBundle:Session')->find($id);
        if( $this->context->getToken() && $this->context->getToken()->getUser() && !is_string($this->context->getToken()->getUser()) ){
            $user = $this->em->getRepository('AppBundle:User')->find($this->context->getToken()->getUser()->getId());
            if(!$session->getUser()){
                $session->setUser($user);
                $user->addSession($session);
                $this->em->persist($session);
                $this->em->flush();
            }
        }elseif($session && $session->getUser()){
            $session->getUser()->removeSession($session);
            $session->setUser(null);
            $this->em->persist($session);
            $this->em->flush();
        }

       parent::gc($max);
    }
*/
}