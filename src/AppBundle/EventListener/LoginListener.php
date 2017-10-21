<?php

namespace AppBundle\EventListener;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class LoginListener implements EventSubscriberInterface
{
    private $container;
    private $em;

    public function __construct($container,\Doctrine\ORM\EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onLogin',
        );
    }

    public function onLogin($event)
    {

      #  die("LOGI LISTENER");
/*
        $session_id =  $this->container->get('session')->getId() ;
        $session = $this->em->getRepository('AppBundle:Session')->find($session_id);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();


        if( $user && $session ){
            $user = $this->em->getRepository('AppBundle:User')->find($user->getId());
            if(!$session->getUser()){
                $session->setUser($user);
                $user->addSession($session);
                $this->em->persist($session);
                $this->em->flush();
            }

        }
*/
    }
}