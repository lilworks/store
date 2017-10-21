<?php
namespace LilWorks\StoreBundle\Service;


use LilWorks\StoreBundle\Entity\OnlineDestocking;
use LilWorks\StoreBundle\Entity\Order;

class OrderUtils
{
    private $em;
    private $order;
    private $mode;
    private $context;

    public function __construct(\Doctrine\ORM\EntityManager $em , $mode,$context )
    {
        $this->em = $em;
        $this->mode = $mode;
        $this->context = $context;

        return $this;
    }

    public function setOrder(Order $order){
        $this->order = $order;
        return $this;
    }

    public function manageStock(){

        if( $this->order->getOrderType() &&
            (
                ($this->order->getOrderType()->getTag() == "FACTURE" || $this->order->getOrderType()->getTag() == "FACTURE_ONLINE" )  &&
                ( $this->haveStep("DONE") || $this->haveStep("PAYED") )
            )
        ){
            foreach($this->order->getOrdersProducts() as $orderProduct){
                if(!$orderProduct->getDestocking()){
                    $orderProduct->setDestocking(true);
                    $newStock = intval($orderProduct->getProduct()->getStock() - $orderProduct->getQuantity());
                    $orderProduct->getProduct()->setStock(
                        ($newStock<0) ? 0 : $newStock
                    );

                    var_dump("HERE DESTOCKINK",$this->mode);
                    if($this->context == "online"){
                        var_dump("ONLINE DESTOCKINK");
                        $onlineDestocking = new OnlineDestocking();
                        $onlineDestocking->setOrderProduct($orderProduct);
                        $this->em->persist($onlineDestocking);
                    }
                }
            }
            $this->em->flush();
        }
    }

    public function getNextReference(){
        if(!$this->order->getOrderType())
            return null;

        $prefix = $this->order->getOrderType()->getPrefix();

        $year = ($this->order->getCreatedAt()) ? $this->order->getCreatedAt()->format('Y') : date('Y') ;


        $nextIndex = $this->em->getRepository("LilWorksStoreBundle:Order")->getNextReference($year,$prefix);
        $zeroLeft = 4 - count($nextIndex);
        for($i=0;$i<$zeroLeft;$i++){
            $nextIndex =  "0" . $nextIndex;
        }

        return "$year-$prefix$nextIndex";

    }
    public function haveStep($step){
        foreach ($this->order->getOrdersOrderSteps() as $orderOrderStep){
            if($orderOrderStep->getOrderStep()->getTag()==$step)
                return true;
        }
        return false;
    }
    public function getLastStep(){
        return $this->em->getRepository("LilWorksStoreBundle:Order")->getLastStep($this->order->getId());
    }
}