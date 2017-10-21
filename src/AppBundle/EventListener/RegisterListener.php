<?php
// AppBundle/EventListener/PasswordResettingListener.php

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use LilWorks\StoreBundle\Entity\PhoneNumber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegisterListener implements EventSubscriberInterface
{
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS  => 'addSuccessFlash',
        );
    }

    public function addSuccessFlash(FormEvent $event)
    {

        /*
        $form = $event->getForm();
        $user = $form->getData();
        $customer = $user->getCustomer();
        $customer->setUser($user);
        $phonenumbers = $customer->getPhonenumbers();
        var_dump(count($phonenumbers));
        var_dump($form->get('customer')->get('phonenumbers')->getData());
        foreach($phonenumbers as $phonenumber){
            $phonenumber->setCustomer($customer);
            $this->em->persist($phonenumber);
        }


        $this->em->persist($customer);
        $this->em->flush();


        //var_dump($form->get('customer')->get('phonenumbers'));
        die();
           */
    }
}