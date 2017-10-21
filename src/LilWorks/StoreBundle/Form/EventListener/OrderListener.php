<?php
namespace LilWorks\StoreBundle\Form\EventListener;


use LilWorks\StoreBundle\Entity\OrdersProducts;
use LilWorks\StoreBundle\Form\OrdersProductsType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class OrderListener implements EventSubscriberInterface
{

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /**
     * Constructor
     *
     * @param Doctrine $doctrine
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
        $this->em = $doctrine->getManager();

    }

    public static function getSubscribedEvents()
    {
        return array(

            FormEvents::PRE_SET_DATA   => 'onPreSetData',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit',
            FormEvents::POST_SUBMIT   => 'onPostSubmit'

        );
    }
    public function onPreSetData(FormEvent $event)
    {
        $order = $event->getData();
        $form = $event->getForm();


        $form->add('ordersProducts', CollectionType::class, array(
            'mapped'=>true,
            'allow_add'=>true,
            'required' => false,
            'allow_delete' => true,
            'empty_data'=>null,
            'delete_empty' => true,
            'entry_type'   => OrdersProductsType::class
        ));
        $newOrderProduct = new OrdersProducts();

        $newOrderProduct->setOrder($order);
        $newOrderProduct->setQuantity(1);
        $order->addOrdersProduct($newOrderProduct);

    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $order = $form->getData();

        foreach($data['ordersProducts'] as $k=>$orderProduct){
            if(!$orderProduct["product"] || $orderProduct["product"]==""){
                unset($data['ordersProducts'][$k]);
            }elseif( intval($orderProduct["quantity"]) <= 0){

            }
        }


        $event->setData($data);
    }
    public function onPostSubmit(FormEvent $event)
    {

        $form = $event->getForm();
        $order = $form->getData();

        foreach($order->getOrdersProducts() as $orderProduct){

            $orderProduct->setName($orderProduct->getProduct()->getBrand()->getName()." | ".$orderProduct->getProduct()->getName());
            $orderProduct->setIsSecondHand($orderProduct->getProduct()->getIsSecondHand());

            if($orderProduct->getQuantity()<=0){
                $this->em->remove($orderProduct);
            }else{
                $this->em->persist($orderProduct);
            }
        }
        $this->em->flush();

    }
}