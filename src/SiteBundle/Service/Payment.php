<?php
namespace SiteBundle\Service;



use SiteBundle\Service\Payment\PaymentInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class Payment
{

    protected $security;
    protected $templating;
    protected $requestStack;
    protected $em;
    protected $datas;
    private $rootDir;
    private $payment;


    public function __construct(\Doctrine\ORM\EntityManager $em  , $templating  ,RequestStack $requestStack,$security, $rootDir)
    {
        $this->security = $security;
        $this->templating = $templating;
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->rootDir = $rootDir;

        return $this;
    }


    public function setPayment($payment,$order){
        $this->datas = $payment->getDatas();
        if(isset($this->datas["MODULE_NAME"])){
            $className = "SiteBundle\\Service\\Payment\\" . ucfirst($this->datas["MODULE_NAME"]);
            $this->payment = new $className($this->datas,$order,$this->rootDir);
        }

        return $this;
    }

    public function getForm(){
        return $this->payment->getForm();
    }
    public function getName(){
        return $this->payment->getName();
    }
    public function getText(){
        return $this->payment->getText();
    }
    public function getRedirection(){
        return $this->payment->getRedirection();
    }
}
