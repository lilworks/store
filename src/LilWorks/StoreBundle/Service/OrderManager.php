<?php
namespace LilWorks\StoreBundle\Service;


use LilWorks\StoreBundle\Entity\Address;
use LilWorks\StoreBundle\Entity\Customer;
use LilWorks\StoreBundle\Entity\OnlineDestocking;
use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Entity\OrderProductReturn;
use LilWorks\StoreBundle\Entity\OrdersProducts;
use LilWorks\StoreBundle\Entity\OrdersOrderSteps;
use Symfony\Component\Validator\Constraints\DateTime;

class OrderManager
{
    private $em;
    private $stockManager;
    private $makeFlush = false;
    private $order;
    private $context;


    public $hasDraft = false;
    public $hasNew = false;
    public $hasDone = false;
    public $hasCanceled = false;
    public $hasPayed = false;
    public $hasPartialPayment = false;

    public $isPayed =false ;
    public $isPartialPayment =false;
    public $manageRef = false ;
    public $manageStock = false;
    public $allowedSteps = array();
    public $forbiddenSteps = array();

    public function __construct(\Doctrine\ORM\EntityManager $em , $stockManager , $context )
    {
        $this->em = $em;
        $this->context = $context;
        $this->stockManager = $stockManager;
        return $this;
    }

    public function isRemovable(Order $order)
    {
        $this->order = $order;

        foreach($this->order->getOrdersOrderSteps() as $orderOrderStep){
            if($orderOrderStep->getOrderStep()->getTag() == "DONE" ||
                $orderOrderStep->getOrderStep()->getTag() == "PAYED" ||
                $orderOrderStep->getOrderStep()->getTag() == "PARTIAL_PAYMENT"
            ){
                return false;
            }

        }

        return true;
        if($this->context == "online"){
            if($this->order->getPayed() == 0 ){
                return true ;
            }
            return false;
        }else{

        }
    }
    public function isLockByReference(Order $order)
    {

        $this->order = $order;

        if(is_null($this->order->getOrderType()))
            return false;

        if ($this->em->getRepository("LilWorksStoreBundle:Order")->isLastIndex($this->order,$this->order->getOrderType()->getPrefix())){
            return false;
        }else{
            return true;
        }

    }
    public function returnAllowed(Order $order)
    {

        $this->order = $order;


        foreach($this->order->getOrdersOrderSteps() as $orderOrderStep){
            if($orderOrderStep->getOrderStep()->getTag() == "DONE")
                return true;
        }


        return false;

    }
    public function allowedSteps(Order $order)
    {
        $steps = array();
        $this->order = $order;

        $this->totCalculator();
        $this->payedCalculator();
        $this->reference();

        if($this->order->getOrderType()->getTag() == "DEVIS"){
            return $this->order;
        }


        foreach ($this->order->getOrdersOrderSteps() as $orderOrderStep) {
            if ($orderOrderStep->getOrderStep()->getTag() == "NEW") {
                if (!$this->hasNew) {
                    $this->hasNew = $orderOrderStep;
                } else {
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if ($orderOrderStep->getOrderStep()->getTag() == "DONE") {
                if (!$this->hasDone) {
                    $this->hasDone = $orderOrderStep;
                } else {
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if ($orderOrderStep->getOrderStep()->getTag() == "PAYED") {
                if (!$this->hasPayed) {
                    $this->hasPayed = $orderOrderStep;
                } else {
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if ($orderOrderStep->getOrderStep()->getTag() == "PARTIAL_PAYMENT") {
                if (!$this->hasPartialPayment) {
                    $this->hasPartialPayment = $orderOrderStep;
                } else {
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if ($orderOrderStep->getOrderStep()->getTag() == "CANCELED") {
                if (!$this->hasCanceled) {
                    $this->hasCanceled = $orderOrderStep;
                } else {
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if ($orderOrderStep->getOrderStep()->getTag() == "DRAFT") {
                if (!$this->hasDraft) {
                    $this->hasDraft = $orderOrderStep;
                } else {
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
        }


        if ($order->getTot() == $order->getPayed()) {
            $this->isPayed = true;
        } elseif ($this->order->getPayed() > 0) {
            $this->isPartialPayment = true;
        }
        if ($this->isPayed && !$this->hasDraft) {
            $this->manageStock = true;
        }
        if (!$this->hasDraft && ($this->isPayed || $this->isPartialPayment)) {
            $this->manageRef = true;
        }

        if($this->hasDraft) {
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PREPARING"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("SHIPPING"));
            #array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PARTIAL_PAYMENT"));
            #array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PAYED"));
        }elseif($this->isPayed){
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("DONE"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PREPARING"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("SHIPPING"));
            #array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PAYED"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("CANCELED"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("DRAFT"));
        }elseif($this->isPartialPayment){
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PREPARING"));
            #array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PAYED"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("CANCELED"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("DRAFT"));
        }elseif($this->hasDone) {
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("CANCELED"));
        }elseif($this->hasCanceled) {
            $steps = [];
            #array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("CANCELED"));
        }else{
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PREPARING"));
            #array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PAYED"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("CANCELED"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("DRAFT"));
            array_push($steps,$this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("NEW"));
        }

        return $steps;
    }


    public function init(Order $order){


        $this->isPayed = false;
        $this->isPartialPayment = false;
        $this->hasDone = false;
        $this->hasPayed = false;
        $this->hasNew =false;
        $this->hasPartialPayment = false;
        $this->hasCanceled = false;
        $this->hasDraft = false;

        $this->order = $order;

        $this->totCalculator();
        $this->payedCalculator();

       // echo "<h1>".$this->order->getTot()."</h1>";
       // echo "<h1>".$this->order->getPayed()."</h1>";


        if($this->order->getOrderType()->getTag() == "DEVIS"){
            $this->reference();
            return $this->order;
        }elseif($this->order->getOrderType()->getTag() == "FACTURE_ONLINE"){
            $this->reference();
            #return $this->order;
        }

        foreach($this->order->getOrdersOrderSteps() as $orderOrderStep){

            if($orderOrderStep->getOrderStep()->getTag() == "NEW"){
                if($this->hasNew){
                    $this->hasNew = $orderOrderStep;
                    $this->em->persist($this->hasNew);
                    //echo "<h1>NEW</h1>";
                }else{
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if($orderOrderStep->getOrderStep()->getTag() == "DONE"){
                if(!$this->hasDone){
                    $this->hasDone = $orderOrderStep;
                    $this->hasDone->setOrder($this->order);
                    $this->em->persist($this->hasDone);
                    //echo "<h1>DONE</h1>";
                }else{
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if($orderOrderStep->getOrderStep()->getTag() == "PAYED"){
                if(!$this->hasPayed){
                    $this->hasPayed = $orderOrderStep;
                    $this->em->persist($this->hasPayed);
                    //echo "<h1>PAYED</h1>";
                }else{
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if($orderOrderStep->getOrderStep()->getTag() == "PARTIAL_PAYMENT"){
                if(!$this->hasPartialPayment){
                    $this->hasPartialPayment = $orderOrderStep;
                    $this->em->persist($this->hasPartialPayment);
                    //echo "<h1>PARTIAL_PAYMENT</h1>";
                }else{
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if($orderOrderStep->getOrderStep()->getTag() == "CANCELED"){
                if(!$this->hasCanceled){
                    $this->hasCanceled = $orderOrderStep;
                    $this->em->persist($this->hasCanceled);
                    //echo "<h1>CANCELED</h1>";
                    $this->hasCanceled = $orderOrderStep;
                }else{
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
            if($orderOrderStep->getOrderStep()->getTag() == "DRAFT"){
                if(!$this->hasDraft){
                    $this->hasDraft = $orderOrderStep;
                    $this->hasDraft->setOrder($this->order);
                    $this->em->persist($this->hasDraft);
                    //echo "<h1>DRAFT</h1>";
                    $this->hasDraft = $orderOrderStep;
                }else{
                    $this->em->remove($orderOrderStep);
                    $this->order->removeOrdersOrderStep($orderOrderStep);
                }
            }
        }
//echo "<hr>";


       // var_dump($order->getTot());
       // var_dump($order->getPayed());

        if( floatval($order->getTot()) === floatval($order->getPayed())){
            //echo "<h1>CALCULATOR PAYED</h1>";
            $this->isPayed = true;
        }elseif( $this->order->getPayed() > 0){
            //echo "<h1>CALCULATOR PARTIAL</h1>";
            $this->isPartialPayment = true;
        }else{
            //echo "<h1>CALCULATOR DONT SET ANYTHING</h1>";
        }

       // var_dump($this->isPayed);
       // var_dump($this->isPartialPayment);

        if($this->isPayed && !$this->hasDraft){
            $this->manageStock = true;
        }
        if(!$this->hasDraft && ($this->isPayed||$this->isPartialPayment)){
            $this->manageRef = true;
        }


        if($this->isPartialPayment){
            //echo "<h1>IS PARTIAL</h1>";
            if(! $this->hasPartialPayment){
                $orderOrderStep = new OrdersOrderSteps();
                $orderOrderStep->setCreatedAt(new \DateTime());
                $orderOrderStep->setOrderStep($this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PARTIAL_PAYMENT"));
                $orderOrderStep->setOrder($this->order);
                $this->order->addOrdersOrderStep($orderOrderStep);
                $this->em->persist($orderOrderStep);
                $this->hasPartialPayment = $orderOrderStep;
            }
            if($this->hasPayed ){
                //echo "<h1>REMOVE THE OLDER PAYED</h1>";
                if($this->hasPartialPayment){
                    $this->order->removeOrdersOrderStep($this->hasPayed);
                    $this->em->remove($this->hasPayed);
                    $this->hasPayed = false;
                }
            }
        }elseif($this->isPayed){
            //echo "<h1>IS PAYED</h1>";
            if(false === $this->hasPayed ){
                $orderOrderStep = new OrdersOrderSteps();
                $orderOrderStep->setCreatedAt(new \DateTime());
                $orderOrderStep->setOrderStep($this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PAYED"));
                $orderOrderStep->setOrder($this->order);
                $this->order->addOrdersOrderStep($orderOrderStep);
                $this->em->persist($orderOrderStep);
                $this->hasPayed = $orderOrderStep;
            }
            if($this->hasPartialPayment ){
                if($this->hasPartialPayment){
                    $this->order->removeOrdersOrderStep($this->hasPartialPayment);
                    $this->em->remove($this->hasPartialPayment);
                    $this->hasPartialPayment = false;
                }
            }
        }else{
            //echo "<h1>NOT PAYED OR PARTIAL</h1>";
            if($this->hasPartialPayment){
                $this->order->removeOrdersOrderStep($this->hasPartialPayment);
                $this->em->remove($this->hasPartialPayment);
                $this->hasPartialPayment = false;
            }
            if($this->hasPayed){
                $this->order->removeOrdersOrderStep($this->hasPayed);
                $this->em->remove($this->hasPayed);
                $this->hasPayed = false;
            }
        }

        if($this->hasDraft){
            //echo "<h1>HAS DRAFT</h1>";
            if($this->hasDone){
                $this->order->removeOrdersOrderStep($this->hasDone);
                $this->em->remove($this->hasDone);
                $this->hasDone = false;
            }
            if($this->hasCanceled){
                $this->order->removeOrdersOrderStep($this->hasCanceled);
                $this->em->remove($this->hasCanceled);
                $this->hasCanceled = false;
            }
            if($this->hasNew){
                $this->order->removeOrdersOrderStep($this->hasNew);
                $this->em->remove($this->hasNew);
                $this->hasNew = false;
            }
            $this->noDestocking();
        }elseif($this->hasCanceled){
            //echo "<h1>HAS CANCELED</h1>";
            if($this->hasDone){
                $this->order->removeOrdersOrderStep($this->hasDone);
                $this->em->remove($this->hasDone);
                $this->hasDone = false;
            }
            $this->noDestocking();
        }elseif($this->hasPayed){
            //echo "<h1>HAS PAYED</h1>";
            $this->destocking();
        }elseif(!$this->hasNew && count($this->order->getOrdersOrderSteps())==0){
            //echo "<h1>CREATE NEW</h1>";
            $orderOrderStep = new OrdersOrderSteps();
            $orderOrderStep->setCreatedAt(new \DateTime());
            $orderOrderStep->setOrderStep($this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("NEW"));
            $orderOrderStep->setOrder($this->order);
            $this->order->addOrdersOrderStep($orderOrderStep);
            $this->em->persist($orderOrderStep);
            $this->hasPayed = $orderOrderStep;
            $this->noDestocking();
        }else{
            $this->noDestocking();
        }
        return $this->order;


    }

    public function destocking(){
        foreach($this->order->getOrdersProducts() as $orderProduct){
            $yetDestocked = $orderProduct->getDestocking();
            $tot = $orderProduct->getQuantity();
            $toDestock = $tot - $yetDestocked;
            $product  = $orderProduct->getProduct();
            $product->setStock($product->getStock()-$toDestock);
            $orderProduct->setDestocking($orderProduct->getQuantity());
            $this->em->persist($product);
            $this->em->persist($orderProduct);
            //var_dump("Destock" , $toDestock);
        }
    }
    public function noDestocking(){
        foreach($this->order->getOrdersProducts() as $orderProduct){
            $yetDestocked = $orderProduct->getDestocking();
            $toRestock = $yetDestocked;
            $product  = $orderProduct->getProduct();
            $product->setStock($product->getStock()+$toRestock);
            $orderProduct->setDestocking(0);
            $this->em->persist($product);
            $this->em->persist($orderProduct);

            //var_dump("Restock" , $toRestock);
        }
    }

    public function setMakeFlush($makeFlush = false){
        $this->makeFlush = $makeFlush;
        return $this;
    }

    public function deleteOrder(){

    }
    public function setOrder(Order $order,$manualCustomer=array()){
        $this->order = $order;
        $this->customer($manualCustomer);
        $this->totCalculator();
        $this->payedCalculator();
        $this->autoOrderStep();

        //$this->stockManager->setMakeFlush(true)->byOrder($this->order);

        $this->reference();

        $this->em->persist($this->order);

        if($this->makeFlush){
            $this->em->flush();
        }
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
    public function customer($manualCustomer){
        if(!$this->order->getCustomer()){
            $customer = new Customer();
            $customer->setFirstName($manualCustomer['firstName']);
            $customer->setLastName($manualCustomer['lastName']);
            $customer->setCompanyName($manualCustomer['companyName']);
            $customer->addOrder($this->order);
            $this->order->setCustomer($customer);
            $this->em->persist($customer);
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

    public function destock(){
        foreach($this->order->getOrdersProducts() as $orderProduct){
            $notYetDestocked = $orderProduct->getQuantity() - $orderProduct->getDestocking() ;
            $orderProduct->setDestocking($orderProduct->getQuantity());
            $product = $orderProduct->getProduct();
            $product->setStock($product->getStock()-$notYetDestocked);
            $this->em->persist($orderProduct);
            $this->em->persist($product);
        }
    }
    public function restock(){

        foreach($this->order->getOrdersProducts() as $orderProduct){
            $yetDestocked =  $orderProduct->getDestocking() ;
            $orderProduct->setDestocking(0);
            $product = $orderProduct->getProduct();
            $product->setStock($product->getStock()+$yetDestocked);
            $this->em->persist($orderProduct);
            $this->em->persist($product);
        }
    }
    public function restockByOrderProduct($orderProduct){
        $product = $orderProduct->getProduct();
        $product->setStock($product->getStock() + $orderProduct->getDestocking());
        $this->em->persist($product);
    }
    public function removeOrder($order){
        $this->order = $order;
        foreach($this->order->getOrdersProducts() as $orderProduct){
            $this->restockByOrderProduct($orderProduct);
        }
    }
    public function autoOrderStep(){


        if($this->order->getOrderType()->getTag() == "DEVIS")
            return null;


       if(! $this->em->getRepository("LilWorksStoreBundle:Order")->haveStep($this->order->getId(),"PAYED") && $this->order->getPayed()>0 && ( $this->order->getTot() == $this->order->getPayed())){
           $orderOrderStep = new OrdersOrderSteps();
           $orderOrderStep->setOrder($this->order);
           $orderOrderStep->setCreatedAt(new \DateTime());
           $orderOrderStep->setOrderStep($this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PAYED"));
           $this->order->addOrdersOrderStep($orderOrderStep);
           $this->em->persist($orderOrderStep);

           $this->destock();

       }elseif( $this->order->getTot()!=$this->order->getPayed()){
           $this->restock();
           foreach($this->order->getOrdersOrderSteps() as $orderOrderStep){
               if($orderOrderStep->getOrderStep()->getTag() == "PAYED"){
                   $this->order->removeOrdersOrderStep($orderOrderStep);
                   $this->em->remove($orderOrderStep);

               }
               if($this->order->getPayed()>0){
                   $orderStepPartial = $this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("PARTIAL_PAYMENT");
                   if(!$orderOrderStep = $this->em->getRepository("LilWorksStoreBundle:OrdersOrderSteps")->findOneBy(array(
                       'order'=>$this->order->getId(),
                       'orderStep'=>$orderStepPartial->getId()
                   ))){
                       $orderOrderStep = new OrdersOrderSteps();
                   }

                   $orderOrderStep->setOrder($this->order);
                   $orderOrderStep->setCreatedAt(new \DateTime());
                   $orderOrderStep->setOrderStep($orderStepPartial);
                   $this->order->addOrdersOrderStep($orderOrderStep);
                   $this->em->persist($orderOrderStep);
               }
           }
       }

        if(count($this->order->getOrdersOrderSteps()) == 0){
            $orderOrderStep = new OrdersOrderSteps();
            $orderOrderStep->setOrder($this->order);
            $orderOrderStep->setCreatedAt(new \DateTime());
            $orderOrderStep->setOrderStep($this->em->getRepository("LilWorksStoreBundle:OrderStep")->findOneByTag("NEW"));
            $this->order->addOrdersOrderStep($orderOrderStep);
            $this->em->persist($orderOrderStep);
        }
    }
}