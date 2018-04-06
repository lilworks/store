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
    private $basketService;
    private $container;
    private $em;

    public function __construct($container,\Doctrine\ORM\EntityManager $em,$basketService)
    {
        $this->container = $container;
        $this->em = $em;
        $this->basketService = $basketService;
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

        $this->basketService->setDefaultAddress(true);

        $session = $this->container->get('session');
        $token = $this->container->get('security.token_storage');

        $session = $this->em->getRepository('AppBundle:Session')->find($session->getId());
        if( $token->getToken() && $token->getToken()->getUser() && !is_string($token->getToken()->getUser()) ){
            $user = $this->em->getRepository('AppBundle:User')->find($token->getToken()->getUser()->getId());
            if(!$session->getUser()){
                $session->setUser($user);
                $user->addSession($session);
                $this->em->persist($session);
                $this->em->flush();
            }
        }

    }
}