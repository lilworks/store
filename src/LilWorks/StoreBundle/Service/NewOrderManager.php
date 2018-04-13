<?php
namespace LilWorks\StoreBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Entity\OrdersOrderSteps;

class NewOrderManager
{
    private $em;
    private $context;
    private $order;
    private $stockManager;

    public function __construct(EntityManagerInterface $em, $stockManager , $context)
    {
        $this->em = $em;
        $this->context = $context;
        $this->stockManager = $stockManager;
        return $this;
    }

    public function manage(Order $order)
    {

        $this->setOrder($order);
        $this->reference();
        $this->totCalculator();
        $this->payedCalculator();
        $this->manageOrderSteps();

        $this->stockManager->manage($order);


        return $this->order;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;
        return $this;
    }


    public function reference(){

        if(!$this->order->getOrderType())
            return null;

        if(!$this->order->getReference() || $this->order->getReference() == ""){

            $prefix = $this->order->getOrderType()->getPrefix();
            $year = ($this->order->getCreatedAt()) ? $this->order->getCreatedAt()->format('Y') : date('Y') ;
            $nextIndex = $this->em->getRepository("LilWorksStoreBundle:Order")->getNextReference($year,$prefix);

            $zeroLeft = 5 - strlen(strval($nextIndex));

            for($i=0;$i<$zeroLeft;$i++){
                $nextIndex =  "0" . $nextIndex;
            }

            $this->order->setReference("$year-$prefix$nextIndex");
        }
    }


    public function totCalculator(){
        $tot = 0;
        foreach($this->order->getOrdersProducts() as $orderProduct){
            if(!$orderProduct->getPrice()){
                $price = ( $this->context == "online") ? $orderProduct->getProduct()->getPriceOnline() :$orderProduct->getProduct()->getPriceOffline() ;
            }else{
                $price=$orderProduct->getPrice();
            }
            $q = ( $orderProduct->getQuantity() && $orderProduct->getQuantity() != 0) ? $orderProduct->getQuantity() :1 ;
            $tot+=$q * $price;
        }
        foreach($this->order->getOrdersRealShippingMethods() as $orderRealShippingMethod){
            $tot+= $orderRealShippingMethod->getPrice();
        }
        $this->order->setTot($tot);
    }


    public function payedCalculator(){

        $payed = 0;
        foreach($this->order->getOrdersPaymentMethods() as $orderPaymentMethod){
            $payed+=$orderPaymentMethod->getAmount();
        }
        $this->order->setPayed($payed);
    }


    public function manageOrderSteps(){


        if(count($this->order->getOrdersOrderSteps()) == 0){

            $newOrderStep = new OrdersOrderSteps();
            $newOrderStep->setOrderStep($this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("NEW"));
            $newOrderStep->setOrder($this->order);

        }elseif( $this->order->getPayed() > 0){

            if( $this->order->getPayed() == $this->order->getTot() ){
                $newOrderStep = new OrdersOrderSteps();
                $newOrderStep->setOrderStep($this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PAYED"));
                $newOrderStep->setOrder($this->order);

            }elseif( $this->order->getPayed() < $this->order->getTot() ){
                $newOrderStep = new OrdersOrderSteps();
                $newOrderStep->setOrderStep($this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PARTIAL_PAYMENT"));
                $newOrderStep->setOrder($this->order);
            }

        }

        if(isset($newOrderStep))
            $this->em->persist($newOrderStep);

    }

}