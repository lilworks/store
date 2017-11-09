<?php
namespace SiteBundle\Service\Payment;


class Cheque implements PaymentInterface
{
    private $datas;

    public function  __construct($datas,$order,$rootDir){
        $this->datas = $datas;
    }

    public function  getForm(){
        return null;
    }

    public function  getName(){
        return $this->datas['_name'];
    }

    public function  getText(){
        return $this->datas['_text'];
    }
    public function getRedirection(){
        return null;
    }
}
