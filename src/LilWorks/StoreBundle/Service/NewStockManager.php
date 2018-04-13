<?php
namespace LilWorks\StoreBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Entity\OrdersProducts;


class NewStockManager
{
    private $em;
    private $context;
    private $mode;
    private $order;

    public function __construct(EntityManagerInterface $em,$context,$mode)
    {
        $this->em = $em;
        $this->context = $context;
        $this->mode = $mode;


        return $this;
    }

    public function manage(Order $order){
        $this->setOrder($order);

        ($this->context == "offline")?
            $this->manageOffline():
            $this->manageOnline();
    }

    public function setOrder(Order $order){
        $this->order = $order;
        return $this;
    }


    public function restoreOnRemove(Order $order){
        foreach($this->order->getOrdersProducts() as $orderProduct){
            // Product need to exist
            if( $orderProduct->getProduct() ){
                if($orderProduct->getDestocking()>0){
                    $orderProduct->getProduct()->setStock(
                        $orderProduct->getProduct()->getStock() + $orderProduct->getDestocking()
                    );
                }
                $this->em->persist($orderProduct->getProduct());
            }
        }
    }

    public function restockByOrderProduct(OrdersProducts $ordersProducts){
        if($ordersProducts->getProduct() && $ordersProducts->getDestocking() > 0){
            $ordersProducts->getProduct()->setStock(
                $ordersProducts->getProduct()->getStock() + $ordersProducts->getDestocking()
            );
            $this->em->persist($ordersProducts->getProduct());
        }
    }
    public function manageOffline(){


        // if DEVIS nothing to do
        if($this->order->getOrderType()->getTag() == "DEVIS")
            return ;


        if( $this->order->getOrderType()->getTag() == "FACTURE" ||
            $this->order->getOrderType()->getTag() == "FACTURE_ONLINE" ){

            if( $this->order->getPayed() == $this->order->getTot() ){
                // Need to destock
                foreach($this->order->getOrdersProducts() as $orderProduct){
                    // Product need to exist
                    if( $orderProduct->getProduct() ){
                        // first restock what it was destocked
                        $orderProduct->getProduct()->setStock(
                            $orderProduct->getProduct()->getStock() +  $orderProduct->getDestocking()
                        );
                        $orderProduct->setDestocking(0);
                        // now destock whole quantity
                        $orderProduct->setDestocking($orderProduct->getQuantity());
                        $orderProduct->getProduct()->setStock(
                            $orderProduct->getProduct()->getStock() - $orderProduct->getQuantity()
                        );

                        ($orderProduct->getProduct()->getStock()<0)?
                            $orderProduct->getProduct()->setStock(0):null;

                        $this->em->persist($orderProduct->getProduct());
                        $this->em->persist($orderProduct);
                    }
                }
            }elseif( $this->order->getPayed() != $this->order->getTot() ){
                //  If products are destoked need to restock
                foreach($this->order->getOrdersProducts() as $orderProduct){
                    if( $orderProduct->getProduct()){
                        $orderProduct->getProduct()->setStock(
                            $orderProduct->getProduct()->getStock() +  $orderProduct->getDestocking()
                        );
                        $orderProduct->setDestocking(0);
                        $this->em->persist($orderProduct->getProduct());
                        $this->em->persist($orderProduct);
                    }
                }
            }
        }
    }
    public function manageOnline(){

    }


}