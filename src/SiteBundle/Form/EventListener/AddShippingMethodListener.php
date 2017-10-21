<?php
// src/AppBundle/Form/EventListener/AddEmailFieldListener.php
namespace SiteBundle\Form\EventListener;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class AddShippingMethodListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit',
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $basket = $event->getData();
        $form = $event->getForm();


        if($basket->getShippingAddress()){

            $country = $basket->getShippingAddress()->getCountry();
            $countryShippingMethods = $country->getShippingmethodsCountries();


            $form->add('shippingMethod', EntityType::class, array(
                'class'    => 'LilWorksStoreBundle:ShippingMethod' ,
                'choice_label' => function ($obj) { return    $obj->getName() . " " . $obj->getPrice() . "€"  ; },

                'required' => false ,
                'mapped'=> true,
                'expanded' => true ,
                'multiple' => false
            ));
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();
        var_dump("onPreSubmit");
        /*
        if (!$user) {
            return;
        }

        // Check whether the user has chosen to display his email or not.
        // If the data was submitted previously, the additional value that
        // is included in the request variables needs to be removed.
        if (true === $user['show_email']) {
            $form->add('email', EmailType::class);
        } else {
            unset($user['email']);
            $event->setData($user);
        }
        */
    }
}
