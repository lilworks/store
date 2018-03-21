<?php
namespace SiteBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class CountryListener {

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }


    public function setCountry(Event $event) {

        $security_context = $this->container->get('security.token_storage');

        if(is_null($security_context->getToken())){
           return ;
        }
        $user = $security_context->getToken()->getUser();


        $session = $this->container->get('session');
        $em = $this->container->get('doctrine')->getManager();


        // Set User Address
        if(is_object($user)){ // Connected
            $userSelectedCountry = $em->getRepository("AppBundle:User")
                ->createQueryBuilder('u')
                ->select('u.id, co.id as countryId')
                ->leftJoin("u.customer","cu")
                ->leftJoin("cu.addresses","a")
                ->leftJoin("a.country","co")
                ->where('u.id = :u_id')
                ->setParameter('u_id',$user->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;

            $userSelectedCountryId = $userSelectedCountry["countryId"];
        }

        $sessionObject = $em->getRepository("AppBundle:Session")->findOneById($session->getId());

        if($sessionObject)
            $basket = $sessionObject->getBasket();

        // Set Basket Address
        if(isset($basket) && $basket->getShippingAddress()){ // Connected
            $basketSelectedCountryId =  $basket->getShippingAddress()->getCountry()->getId();
        }


        if(isset($basketSelectedCountryId)){
            $defaultCountryId = $basketSelectedCountryId;
        }elseif(isset($userSelectedCountryId)){
            $defaultCountryId = $userSelectedCountryId;
        }elseif(!isset($defaultCountryId)){
            $defaultCountryId = $em->getRepository("LilWorksStoreBundle:Country")->findOneByTag('fr')->getId();
        }


        $session->set('defaultCountryId',$defaultCountryId);
    }

}