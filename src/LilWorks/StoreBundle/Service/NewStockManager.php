<?php
namespace LilWorks\StoreBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use LilWorks\StoreBundle\Entity\Order;


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

    public function setOrder(Order $order){
        $this->order = $order;
        return $this;
    }

    public function manage(Order $order){
        $order = $this->setOrder($order);

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
}