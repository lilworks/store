<?php
namespace LilWorks\StoreBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use LilWorks\StoreBundle\Entity\Order;


class NewStockManager
{
    private $em;
    private $context;
    private $mode;

    public function __construct(EntityManagerInterface $em,$context,$mode)
    {
        $this->em = $em;
        $this->context = $context;
        $this->mode = $mode;


        return $this;
    }

    public function setOrder(Order $order){
        $this->order = $order;
        return $this;
    }

    public function manage(Order $order){
        $order = $this->setOrder($order);


        if( $order->getOrderType()->getTag() == "FACTURE" ||
            $order->getOrderType()->getTag() == "FACTURE_ONLINE" ){

        }

    }


}