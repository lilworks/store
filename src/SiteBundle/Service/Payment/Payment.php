<?php
namespace SiteBundle\Service\Payment;


class Payment implements PaymentInterface
{

    private $data;

    public function set($data){
        $this->data = $data;
    }
    public function  getForm(){
    }

    public function  getName(){
        return null;
    }

    public function  getText(){
        return null;
    }
    public function getRedirection(){
        return null;
    }
}
