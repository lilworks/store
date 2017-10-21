<?php
namespace LilWorks\StoreBundle\Form\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class IsSecondHandListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(

            FormEvents::PRE_SUBMIT   => 'onPreSubmit',



        );
    }


    public function onPreSubmit(FormEvent $event)
    {

        $form = $event->getForm();


        if (false === $form->get('isSecondHand')) {
            $form->add('isSecondHand');
        }
    }
}