<?php
namespace SiteBundle\Service;


use DoctrineExtensions\Query\Sqlite\Date;
use LilWorks\StoreBundle\Entity\BasketsProducts;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Templating\TemplatingExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use LilWorks\StoreBundle\Entity\Product;
use LilWorks\StoreBundle\Entity\Basket as BasketEntity;
use Symfony\Component\HttpFoundation\RedirectResponse;

use SiteBundle\Service\PaymentMethod\Spplus;

class Payment
{

    protected $security;
    protected $templating;
    protected $requestStack;
    protected $em;
    protected $datas;
    private $rootDir;
    private $paymentMethod;


    public function __construct(\Doctrine\ORM\EntityManager $em  , $templating  ,RequestStack $requestStack,$security, $rootDir)
    {
        $this->security = $security;
        $this->templating = $templating;
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->rootDir = $rootDir;

        return $this;
    }


    public function setPaymentMethod($paymentMethod,$order){
        $this->datas = $paymentMethod->getDatas();
        if($this->datas["MODULE_NAME"]){
            $className = "SiteBundle\\Service\\PaymentMethod\\" . ucfirst($this->datas["MODULE_NAME"]);
            $this->paymentMethod = new $className($this->datas,$order,$this->rootDir);
        }
    }
    public function getForm(){
        if($this->paymentMethod){
            return $this->paymentMethod->getForm();
        }
    }
    public function getRedirection(){
        return 'https://paiement.systempay.fr/vads-payment/?'.http_build_query($this->paymentMethod->getSendParameters());
    }
}
