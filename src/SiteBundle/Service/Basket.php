<?php
namespace SiteBundle\Service;


use DoctrineExtensions\Query\Sqlite\Date;
use LilWorks\StoreBundle\Entity\BasketsProducts;
use LilWorks\StoreBundle\Entity\Order;
use LilWorks\StoreBundle\Entity\OrdersOrderSteps;
use LilWorks\StoreBundle\Entity\OrdersProducts;
use Proxies\__CG__\LilWorks\StoreBundle\Entity\OrdersRealShippingMethods;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Templating\TemplatingExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use LilWorks\StoreBundle\Entity\Product;
use LilWorks\StoreBundle\Entity\Basket as BasketEntity;


class ShippingCalculator
{
    public $basket;
    public $basketService;


    protected $em;

    const _DEFAULT_COUNTRY = "fr";

    public $combinations = array();
    private $_products = array();
    private $_productsInBasketDatas = array();
    private $_shippingMethodsProductsPrioritized = array();
    private $_shippingMethodsProducts = array();
    private $_productsShippingMethods = array();
    private $_shippingMethodsCombination = array();
    private $_shippingMethods = array();
    private $_shippingMethodsPriced = array();
    private $_country ;
    private $_shippingMethodsPrioritized = array() ;

    public function __construct(\Doctrine\ORM\EntityManager $em,$basket)
    {
        $this->basketService = $basket;
        $this->basket = $basket->getBasket();
        $this->em = $em;

        $this->init();

       # $this->__toString();
        return $this;
    }
    public function init(){

        if($this->basket->getShippingAddress()){
            $this->_country = $this->basket->getShippingAddress()->getCountry()->getTag();
        }elseif( $this->basket->getUser() ){
            if ( count($this->basket->getUser()->getCustomer()->getAddresses())>0 ){
                $addresses = $this->basket->getUser()->getCustomer()->getAddresses();
                $this->_country = $addresses[0]->getCountry()->getTag();
            }else{
                $this->_country = self::_DEFAULT_COUNTRY;
            }
        }else{
            $this->_country = self::_DEFAULT_COUNTRY;
        }


        $allowedShippingMethod = array();

        if($this->basket->getShippingAddress()){
        $shippingMethodsCountries = $this->em->getRepository('LilWorksStoreBundle:ShippingMethodsCountries')
            ->getShippingMethodsByCountry( $this->basket->getShippingAddress()->getCountry());
        }else{
            $shippingMethodsCountries = array();
        }



        foreach($shippingMethodsCountries as $shippingMethodCountrie){


            array_push($allowedShippingMethod,$shippingMethodCountrie->getShippingMethod()->getId());
            ($shippingMethodCountrie->getPrice()) ? $price= $shippingMethodCountrie->getPrice():$price= $shippingMethodCountrie->getShippingMethod()->getPrice();
            ($shippingMethodCountrie->getFreeTrigger()) ? $freeTrigger=$shippingMethodCountrie->getFreeTrigger():$freeTrigger=$shippingMethodCountrie->getShippingMethod()->getFreeTrigger();
            ($shippingMethodCountrie->getPriority()) ? $priority=$shippingMethodCountrie->getPriority() : $priority=$shippingMethodCountrie->getShippingMethod()->getPriority();

            $shippingMethodPriceValue = $this->em->getRepository('LilWorksStoreBundle:ShippingMethodsCountriesTriggers')
                        ->getPriceByTot($shippingMethodCountrie,$this->basket->getTot());


            $this->_shippingMethodsPriced[$shippingMethodCountrie->getShippingMethod()->getId()] = array(
                //"price"=>$price,
                //"freeTrigger"=>$freeTrigger,
                "priority"=>$priority,
                "freeTrigger"=>9999,
                "price"=>$shippingMethodPriceValue,
            );

        }



        foreach($this->basket->getBasketsProducts() as $product){
            $this->_productsInBasketDatas[$product->getProduct()->getId()]=array("q"=>$product->getQuantity(),"pu"=>$product->getProduct()->getPriceOnline(),"price"=>$product->getQuantity()*$product->getProduct()->getPriceOnline());
            $this->_productsShippingMethods[$product->getProduct()->getId()]=array();

            if(!in_array($product->getProduct()->getId(),$this->_products))
                array_push($this->_products,$product->getProduct()->getId());
            foreach($product->getProduct()->getShippingMethods() as $shippingMethod){
                if(!in_array($shippingMethod->getId(),$this->_shippingMethods) && in_array($shippingMethod->getId(),$allowedShippingMethod)){
                    array_push($this->_shippingMethods,$shippingMethod->getId());

                }
                array_push($this->_productsShippingMethods[$product->getProduct()->getId()],$shippingMethod->getId());
            }
        }

        foreach($this->_shippingMethodsPriced as $shippingMethodId=>$shippingMethodPriced){
            if(!isset($this->_shippingMethodsPrioritized[$shippingMethodPriced["priority"]])){
                $this->_shippingMethodsPrioritized[$shippingMethodPriced["priority"]] = array();
            }
            if(!in_array($shippingMethodId,$this->_shippingMethodsPrioritized[$shippingMethodPriced["priority"]]))
                array_push($this->_shippingMethodsPrioritized[$shippingMethodPriced["priority"]],$shippingMethodId);
        }
        $this->_smPs();


    }


    private function _smPs(){

        $smPs = array();
        $smPsNoPrioritized = array();

        foreach($this->_shippingMethods as $sm){
            $smPs[$sm] = array();
            $smPsNoPrioritized[$sm] = array();

            foreach($this->_shippingMethodsPrioritized as $priority=>$sms){
                if(in_array($sm,$sms)){
                    $currentPriority = $priority;
                    break;
                }
            }

            foreach($this->_productsShippingMethods as $kp=>$p){
                /*
                 * $p est une liste de sm
                 */
                foreach($p as $smInP){
                    foreach($this->_shippingMethodsPrioritized as $priority2=>$sms2){
                        if(in_array($smInP,$sms2)){
                            $pCurrentPriority = $priority2;
                            break;
                        }
                    }
                }


                if(in_array($sm,$p) || (isset($pCurrentPriority) &&  $pCurrentPriority<$currentPriority )){
                    array_push($smPs[$sm],$kp);
                }
                if(in_array($sm,$p)){
                    array_push($smPsNoPrioritized[$sm],$kp);
                }
            }
        }

        $this->_shippingMethodsPrioritized = $smPs;
        $this->_shippingMethods = $smPsNoPrioritized;
        #$this->_cleanSmsPs();
        $this->_getSmsComb();

        foreach($this->_shippingMethodsCombination as $kCombination=>$combination){
            $this->_dispatchProduct($kCombination,$combination);
        }





    }
    private function _dispatchProduct( $kCombination,$combination )
    {



        $products = $this->_products;

        $price = 0;
        $this->combinations[$kCombination]["datas"] = array();
        $this->combinations[$kCombination]["price"] = $price;


        foreach($combination as $shippingMethodId){
            $this->combinations[$kCombination]["datas"][$shippingMethodId] = array();
            $this->combinations[$kCombination]["datas"][$shippingMethodId]["products"] = array();
            $amountInShippingMethod = 0;

            foreach($this->_shippingMethodsPrioritized[$shippingMethodId] as $productInShippingMethod){
                $kProduct = array_keys($products,$productInShippingMethod);
                if(count($kProduct)>0){
                    unset($products[$kProduct[0]]);
                    array_push( $this->combinations[$kCombination]["datas"][$shippingMethodId]["products"],$productInShippingMethod);
                    $amountInShippingMethod+=$this->_productsInBasketDatas[$productInShippingMethod]["price"];
                }
            }


            if($this->_productsInBasketDatas[$productInShippingMethod]["price"] == 0){
                $this->combinations[$kCombination]["datas"][$shippingMethodId]["price"] = 0;
                $this->combinations[$kCombination]["datas"][$shippingMethodId]["freeTrigger"] = true;
            }else{
                $this->combinations[$kCombination]["datas"][$shippingMethodId]["price"] = $this->_shippingMethodsPriced[$shippingMethodId]["price"];
                $this->combinations[$kCombination]["datas"][$shippingMethodId]["freeTrigger"] = false;
                $price+=$this->_shippingMethodsPriced[$shippingMethodId]["price"];
            }
            $this->combinations[$kCombination]["price"]=$price;
        }

    }
    public function __toString( )
    {

            echo "<h1>combinations</h1>";
            var_dump($this->combinations);
        echo "<hr>";
        echo "<h1>_shippingMethods</h1>";
            var_dump($this->_shippingMethods);
        echo "<hr>";
        echo "<h1>_shippingMethodsPriced</h1>";
        var_dump($this->_shippingMethodsPriced);
        echo "<hr>";
        echo "<h1>_shippingMethodsPrioritized</h1>";
        var_dump($this->_shippingMethodsPrioritized);
        echo "<hr>";

        echo "<h1>_products</h1>";
        var_dump($this->_products);
        echo "<hr>";
        echo "<h1>_shippingMethodsCombination</h1>";
        var_dump($this->_shippingMethodsCombination);
        echo "<hr>";

        echo "<h1>_productsShippingMethods</h1>";
        var_dump($this->_productsShippingMethods);
        echo "<hr>";



    }
    private function _cleanSmsPs( )
    {
        /*
         * Doit supprimer toute les sm qui contienne tout les produit de cette sm
         */
        foreach($this->_shippingMethodsPrioritized as $k1=>$listOfSm){
            foreach($this->_shippingMethodsPrioritized as $k2=>$listOfSm2) {
                /*
                 * On compare aux autres
                 */
                if($k1!=$k2){
                    if(count($listOfSm)==count(array_intersect($listOfSm2,$listOfSm))){
                        unset($this->_shippingMethodsPrioritized[$k1]);
                        break;
                    }
                }
            }
        }

    }
    private function _getSmsComb( )
    {
        $sms = array();
        $output = array();
        foreach($this->_shippingMethodsPrioritized as $k=>$v){
            array_push($sms,$k);
        }

        // initialize by adding the empty set
        $results = array(array( ));

        foreach ($sms as $element){
            foreach ($results as $combination){
                array_push($results, array_merge(array($element), $combination));
            }
        }

        foreach($results as $result){
            if(count($result)>0){
                $currentCombProductList = array();
                foreach($result as $smId){
                    foreach($this->_shippingMethodsPrioritized[$smId] as $productHere){
                        if(!in_array($productHere,$currentCombProductList))
                            array_push($currentCombProductList,$productHere);
                    }
                }
                if(count($this->_products) == count($currentCombProductList)){
                    array_push($output,$result);
                }
            }
        }

        /*
         * get minimum sm
         */
        $min = 999;
        foreach($output as $smsComb){
            $c = count($smsComb);
            if($c<$min)
                $min = $c;
        }

        foreach($output as $k=>$smsComb){
            $c = count($smsComb);
            if($c>$min)
                unset($output[$k]);
        }

        $this->_shippingMethodsCombination = $output;
    }
}

class Basket
{

    protected $security;
    protected $templating;
    protected $requestStack;
    protected $em;
    protected $context;
    protected $orderManager;
    public $shippingCalculator;


    public function __construct(\Doctrine\ORM\EntityManager $em  , $templating  ,RequestStack $requestStack,$security,$context,$orderManager)
    {
        $this->security = $security;
        $this->templating = $templating;
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->context = $context;
        $this->orderManager = $orderManager;
        return $this;
    }

    public function index()
    {
        return $this->render('LilWorksStoreBundle:Basket:index.html.twig', array(

        ));
    }

    public function small()
    {

        return $this->templating->render('SiteBundle:Basket:small.html.twig', array(
            'basket'=>$this->getBasket()
        ));

    }

    public function getTotals(){
        $basket = $this->getBasket();

        $totTTC = 0;
        $totHT = 0;
        $SM = 0;

        foreach($basket->getBasketsProducts() as $productInBasket) {
            $totTTC+=$productInBasket->getQuantity()*$productInBasket->getProduct()->getPriceOnline();
        }


        if($basket->getBasketsRealShippingMethods() && $basket->getShippingAddress()){
            foreach($basket->getBasketsRealShippingMethods() as $realShippingMethod){
                $SM+= $realShippingMethod->getPrice();
            }
        }

        return array(
            "TTC"=>$totTTC,
            "HT"=>$totHT,
            "SM"=>$SM
        );
    }

    public function toOrder(){
        $basket = $this->getBasket();

        $order = new Order();


        $order->setCustomer($basket->getUser()->getCustomer());
        $order->setBillingAddress($basket->getBillingAddress());
        $order->setShippingAddress($basket->getShippingAddress());

        $orderStep = $this->em->getRepository('LilWorksStoreBundle:OrderStep')->findOneByTag('NEW');
        $orderOrderStep = new OrdersOrderSteps();
        $orderOrderStep->setOrderStep($orderStep);
        $orderOrderStep->setOrder($order);
        $order->addOrdersOrderStep($orderOrderStep);

        $orderType = $this->em->getRepository('LilWorksStoreBundle:OrderType')->findOneByTag('FACTURE_ONLINE');
        $order->setOrderType($orderType);

        foreach($basket->getBasketsRealShippingMethods() as $basketRealShippingMethod){
            $orderRealShippingMethod = new OrdersRealShippingMethods();
            $orderRealShippingMethod->setReference(null);
            $orderRealShippingMethod->setShippingMethod($basketRealShippingMethod->getShippingMethod());
            $orderRealShippingMethod->setPrice($basketRealShippingMethod->getPrice());

            foreach($basketRealShippingMethod->getBasketsProducts() as $basketProductInRealShippingMethod){
                $orderProduct = new OrdersProducts();
                $orderProduct->setDestocking(false);
                $orderProduct->setOrder($order);
                $orderProduct->setProduct($basketProductInRealShippingMethod->getProduct());
                $orderProduct->setQuantity($basketProductInRealShippingMethod->getQuantity());
                $price = "getPrice".ucfirst($this->context);
                $orderProduct->setPrice($basketProductInRealShippingMethod->getProduct()->$price());
                $orderProduct->setName($basketProductInRealShippingMethod->getProduct()->getBrand()->getName() . " " . $basketProductInRealShippingMethod->getProduct()->getName());
                $order->addOrdersProduct($orderProduct);
            }

            $orderRealShippingMethod->setOrder($order);
        }
/*
        foreach($basket->getBasketsProducts() as $basketProduct){
            if(!$basketProduct->getBasketRealShippingMethod()){
                $orderProduct = new OrdersProducts();
                $orderProduct->setDestocking(false);
                $orderProduct->setOrder($order);
                $orderProduct->setProduct($basketProduct->getProduct());
                $orderProduct->setQuantity($basketProduct->getQuantity());
                $price = "getPrice".ucfirst($this->context);
                $orderProduct->setPrice($basketProduct->getProduct()->$price());
                $orderProduct->setName($basketProduct->getProduct()->getBrand()->getName() . " " . $basketProduct->getProduct()->getName());
                $order->addOrdersProduct($orderProduct);
            }
        }
*/


        $order=$this->orderManager->setMakeFlush(false)->init($order);

        $this->em->persist($order);
        $this->em->remove($basket);
        $this->em->flush();

        return $order;

    }

    public function shippingMethods()
    {

        $this->shippingCalculator = new ShippingCalculator($this->em,$this);
        return $this->shippingCalculator->combinations;
    }

    public function getBasket()
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $session->start();

        if($sessionDb = $this->em->getRepository('AppBundle:Session')->find($session->getId())){
            $basket =   $sessionDb->getBasket();
        }


        if( !isset( $basket )){
            $basket = new BasketEntity();
        }


        return $basket;
    }

    public function createBasket(){
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $sessionDb = $this->em->getRepository('AppBundle:Session')->find($session->getId());

        $basket = new BasketEntity();
        $basket->setToken($sessionDb);
        $sessionDb->setBasket($basket);
        if( count($this->security->getToken()->getRoles()) > 0){
            $user = $this->security->getToken()->getUser();
            $basket->setUser($user);
        }
        $this->em->persist($basket);
        $this->em->flush();


        return $basket;
    }
    public function addProduct(Product $product)
    {

        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        $sessionDb = $this->em->getRepository('AppBundle:Session')->find($session->getId());
        $basket =   $sessionDb->getBasket();
        if( ! $basket ){
            $basket = $this->createBasket();
        }

        if($basket){
            foreach($basket->getBasketsProducts() as $productInBasket){
                if($productInBasket->getProduct()->getId() === $product->getId()){
                    if($productInBasket->getProduct()->getIsSecondHand() !=1)
                    $productInBasket->setQuantity($productInBasket->getQuantity()+1);
                    $notNew = true;
                }
            }
            if(!isset($notNew)){
                $basketsProducts = new BasketsProducts();
                $basketsProducts->setProduct($product);
                $basketsProducts->setBasket($basket);
                $basketsProducts->setQuantity(1);
                $basket->addBasketsProduct($basketsProducts);
            }

            $this->em->persist($basket);
            $this->em->flush();

            return true;
        }
    }

    public function removeProduct(Product $product)
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        $sessionDb = $this->em->getRepository('AppBundle:Session')->find($session->getId());
        $basket =   $sessionDb->getBasket();
        if( ! $basket ){
            $basket = $this->createBasket();
        }

        if($basket){
            foreach($basket->getBasketsProducts() as $productInBasket){
                if($productInBasket->getProduct()->getId() === $product->getId()){
                    $productInBasket->setQuantity($productInBasket->getQuantity()-1);
                    if($productInBasket->getQuantity() <= 0){
                        $this->em->remove($productInBasket);
                        $basket->removeBasketsProduct($productInBasket);
                    }else{
                        $this->em->persist($productInBasket);
                    }
                }
            }

            $this->em->persist($basket);
            $this->em->flush();

            return true;
        }
    }

    public function deleteProduct(Product $product)
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        $sessionDb = $this->em->getRepository('AppBundle:Session')->find($session->getId());
        $basket =   $sessionDb->getBasket();
        if( ! $basket ){
            $basket = $this->createBasket();
        }

        if($basket){
            foreach($basket->getBasketsProducts() as $productInBasket){
                if($productInBasket->getProduct()->getId() === $product->getId()){
                    $this->em->remove($productInBasket);
                    $basket->removeBasketsProduct($productInBasket);
                }
            }

            $this->em->persist($basket);
            $this->em->flush();

            return true;
        }
    }

    public function emptyBasket()
    {

        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $session->start();


        $basket = $this->em->getRepository('LilWorksStoreBundle:Basket')->createQueryBuilder('b')
            ->select('b')
            ->leftJoin('b.token','s')
            ->where('s.id = :id ')
            ->setParameter('id',$session->getId())
            ->getQuery()
            ->getSingleResult()
        ;


        foreach($basket->getBasketsProducts() as $productInBasket){
            $this->em->remove($productInBasket);
            $basket->removeBasketsProduct($productInBasket);
        }

        $this->em->persist($basket);
        $this->em->flush();

        return true;
    }

}