<?php
namespace SiteBundle\Service\Payment;


class Virement extends Payment
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
