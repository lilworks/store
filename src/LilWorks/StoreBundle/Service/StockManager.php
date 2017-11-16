<?php
namespace LilWorks\StoreBundle\Service;


use LilWorks\StoreBundle\Entity\OnlineDestocking;
use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Entity\OrderProductReturn;
use LilWorks\StoreBundle\Entity\OrdersProducts;

class StockManager
{
    private $em;
    private $context;
    private $mode;
    private $log = array();
    private $count = 0;
    private $makeFlush = false;

    public function __construct(\Doctrine\ORM\EntityManager $em,$context,$mode)
    {
        $this->em = $em;
        $this->context = $context;
        $this->mode = $mode;


        return $this;
    }


    public function setMakeFlush($makeFlush = false){
        $this->makeFlush = $makeFlush;
        return $this;
    }

    public function byOrder(Order $order)
    {


        if($order->getOrderType()->getName() == "DEVIS")
            return null;


        $lastStepTag = $this->em->getRepository("LilWorksStoreBundle:Order")->getLastStep($order->getId());
        $currentSteps = $order->getOrdersOrderSteps();
        foreach($currentSteps as $orderOrderStep){
            if($orderOrderStep->getOrderStep()->getTag()=="PAYED" || $orderOrderStep->getOrderStep()->getTag()=="DONE"){
                $lastStepTag == $orderOrderStep->getOrderStep()->getTag();
                break;
            }
        }


        foreach($order->getOrdersProducts() as $orderProduct){

            $maxDestocking = $orderProduct->getQuantity();
            if($orderProduct->getOrderProductReturn()){
                $maxDestocking-=$orderProduct->getOrderProductReturn()->getQuantity();
            }

            if( ($lastStepTag == 'PAYED' || $lastStepTag == 'DONE') && $orderProduct->getDestocking() != $maxDestocking ){

                $orderProduct->setDestocking($maxDestocking);
                $product = $orderProduct->getProduct();
                $product->setStock( $product->getStock() - $maxDestocking );
                $this->em->persist($product);
                $this->count+=-$maxDestocking;
                array_push($this->log,'storebundle.stockmanager.destocked');

                if($this->mode == "slave"){
                    if(! $onlineDestocking =$this->em->getRepository("LilWorksStoreBundle:OnlineDestocking")->findOneByOrderProduct($orderProduct->getId()));
                        $onlineDestocking = new OnlineDestocking();
                    $onlineDestocking->getDestockedAt(new \DateTime());
                    $onlineDestocking->setOrderProduct($orderProduct);
                    $onlineDestocking->setDestocking($maxDestocking);
                    $this->em->persist($onlineDestocking);
                    array_push($this->log,'storebundle.stockmanager.slave.destocked');
                }

            }elseif($orderProduct->getDestocking() == $orderProduct->getQuantity()){
                $orderProduct->setDestocking(0);
                $product = $orderProduct->getProduct();
                $product->setStock( $product->getStock() + $orderProduct->getQuantity() );
                $this->em->persist($product);
                $this->count+=-$orderProduct->getQuantity();
                array_push($this->log,'storebundle.stockmanager.restocked');

                if($this->mode == "slave"){
                    if( $onlineDestocking =$this->em->getRepository("LilWorksStoreBundle:OnlineDestocking")->findOneByOrderProduct($orderProduct->getId())){
                        $this->em->remove($onlineDestocking);
                    }

                    array_push($this->log,'storebundle.stockmanager.slave.removed');
                }
            }
        }

        if($this->makeFlush)
            $this->em->flush();

        return $this->count;

    }
    public function byReturn(OrderProductReturn $orderProductReturn)
    {
        $order = $orderProductReturn->getOrderProduct()->getOrder();
        if($order->getOrderType()->getName() == "DEVIS")
            return null;

        $lastStepTag = $this->em->getRepository("LilWorksStoreBundle:Order")->getLastStep($order->getId());

        $orderProduct = $orderProductReturn->getOrderProduct();
        $product = $orderProductReturn->getOrderProduct()->getProduct();
        if( $lastStepTag == 'PAYED' || $lastStepTag == 'DONE'  ){
            $orderProduct->setDestocking($orderProduct->getQuantity()-$orderProductReturn->getQuantity());
            $product->setStock($product->getStock()+$orderProductReturn->getQuantity());

            $this->count+=-$orderProduct->getQuantity();
            $this->em->persist($orderProduct);
            $this->em->persist($product);
            array_push($this->log,'storebundle.stockmanager.restocking');

            if($this->mode == "slave"){
                if( $onlineDestocking =$this->em->getRepository("LilWorksStoreBundle:OnlineDestocking")->findOneByOrderProduct($orderProduct->getId())){
                    $onlineDestocking->setDestocking($orderProduct->getQuantity() - $orderProductReturn->getQuantity());
                    $this->em->persist($onlineDestocking);
                }

                array_push($this->log,'storebundle.stockmanager.slave.removed');
            }
        }
        if($this->makeFlush)
            $this->em->flush();

        return $this->count;
    }
}